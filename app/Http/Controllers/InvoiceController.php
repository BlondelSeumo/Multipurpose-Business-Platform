<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Invoice;
use App\InvoiceItem;
use App\InvoiceTemplate;
use App\Stock;
use App\Transaction;
use App\Project;
use App\CompanySetting;
use App\Contact;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;
use App\Mail\InvoiceReceiptMail;
use App\Utilities\Overrider;
use Notification;
use App\Notifications\InvoiceCreated;
use App\Notifications\InvoiceUpdated;
use Carbon\Carbon;
use DataTables;
use DB;
use PDF;

class InvoiceController extends Controller
{

	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	

        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'invoice_limit' ) ){
                    return redirect('membership/extend')->with('message',_lang('Your Current package not support this feature. You can upgrade your package !'));
                }

                // If request is create/store
                $route_name = \Request::route()->getName();
                if( $route_name == 'invoices.store'){
                   if( ! has_feature_limit( 'invoice_limit' ) ){
                      if( ! $request->ajax()){
                          return redirect('membership/extend')->with('message', _lang('Your have already reached your usages limit. You can upgrade your package !'));
                      }else{
                          return response()->json(['result'=>'error','message'=> _lang('Your have already reached your usages limit. You can upgrade your package !') ]);
                      }
                   }
                }
            }

            return $next($request);
        });
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.accounting.invoice.list');
    }
	
	
	public function get_table_data(){
		
		$currency = currency();
		$company_id =company_id(); 

		$projects = DB::table('projects')
	                  ->select('id','name as contact_name',DB::raw('"projects" as type'))
	                  ->where('company_id', $company_id);

		$all_contacts = DB::table('contacts')
		                  ->select('id','contact_name',DB::raw('"contacts" as type'))
		                  ->where('company_id', $company_id)
		                  ->union($projects);

        $invoices = Invoice::joinSub($all_contacts, 'all_contacts', function ($join) {
						            $join->on('invoices.related_id', '=', 'all_contacts.id')
						                 ->on('invoices.related_to', '=', 'all_contacts.type');
						        })
		                       ->select("invoices.*", "all_contacts.contact_name", "all_contacts.id as contact_id")
							   ->where('invoices.company_id', $company_id)
	                           ->orderBy('invoices.id', 'desc');                  
						   

		return Datatables::eloquent($invoices)
						->addColumn('contact_name', function ($invoice) {
							if($invoice->related_to == 'contacts'){
								return '<a href="'.action('ContactController@show', $invoice->related_id).'">'.$invoice->contact_name.' <span class="text-muted small">('._lang('Customer').')</span></a>';
							}
							return '<a href="'.action('ProjectController@show', $invoice->related_id).'">'.$invoice->contact_name.' <span class="text-muted small">('._lang('Project').')</span></a>';
						})
						->filterColumn('contact_name', function($query, $keyword) {
		                    $sql = "all_contacts.contact_name  like ?";
		                    $query->whereRaw($sql, ["%{$keyword}%"]);
		                })
						->editColumn('due_date', function ($invoice) {
							$date_format = get_company_option('date_format','Y-m-d');
							return date($date_format, strtotime($invoice->due_date));
						})
						->editColumn('grand_total', function ($invoice) use ($currency){		
						    $acc_currency = currency($invoice->client->currency);
							if($acc_currency != $currency){
								return "<span class='float-right'>".decimalPlace($invoice->grand_total, $currency)."</span><br>
										<span class='float-right'><b>".decimalPlace($invoice->converted_total, $acc_currency)."</b></span>";
							}else{
								return "<span class='float-right'>".decimalPlace($invoice->grand_total, $currency)."</span>";
							}
						})
						->editColumn('status', function ($invoice) {
							return invoice_status($invoice->status);
						})
						->addColumn('action', function ($invoice) {
								return '<div class="dropdown">'
										.'<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">'._lang('Action')
										.'&nbsp;<i class="fas fa-angle-down"></i></button>'
										.'<div class="dropdown-menu">'
											.'<a class="dropdown-item" href="'. action('InvoiceController@edit', $invoice->id) .'"><i class="fas fa-edit"></i> '._lang('Edit') .'</a>'
											.'<a class="dropdown-item" href="'. action('InvoiceController@show', $invoice->id) .'" data-title="'._lang('View Invoice') .'" data-fullscreen="true"><i class="fas fa-eye"></i> '._lang('View') .'</a>'
											.'<a href="'. url('invoices/create_payment/'.$invoice->id) .'" data-title="'. _lang('Make Payment') .'" class="dropdown-item ajax-modal"><i class="fas fa-credit-card"></i> '._lang('Make Payment') .'</a>'
											.'<a href="'. url('invoices/view_payment/'.$invoice->id) .'" data-title="'. _lang('View Payment') .'" data-fullscreen="true" class="dropdown-item ajax-modal"><i class="fas fa-credit-card"></i> '. _lang('View Payment') .'</a>'
												.'<form action="'. action('InvoiceController@destroy', $invoice['id']) .'" method="post">'								
													.csrf_field()
													.'<input name="_method" type="hidden" value="DELETE">'
													.'<button class="button-link btn-remove" type="submit"><i class="fas fa-recycle"></i> '._lang('Delete') .'</button>'
												.'</form>'	
											.'</div>'
										.'</div>';
						})
						->setRowId(function ($invoice) {
							return "row_".$invoice->id;
						})
						->rawColumns(['grand_total','status','action','contact_name'])
						->make(true);							    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.invoice.create');
		}else{
           return view('backend.accounting.invoice.modal.create');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'invoice_number' => 'required|max:191',
            'related_to' => 'required',
            'client_id' => 'required_if:related_to,contacts',
            'project_id' => 'required_if:related_to,projects',
            'invoice_date' => 'required',
            'due_date' => 'required',
            'product_id' => 'required',
            'template' => 'required',
		],[
		   'product_id.required' => _lang('You must select at least one product or service')
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('invoices/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		DB::beginTransaction();
		
	    $company_id = company_id();
		
        $invoice = new Invoice();
	    $invoice->invoice_number = $request->input('invoice_number');
        $invoice->invoice_date = $request->input('invoice_date');
        $invoice->due_date = $request->input('due_date');
        $invoice->grand_total = $request->input('product_total');
        $invoice->tax_total = $request->input('tax_total');
        $invoice->paid = 0;
        $invoice->status = 'Unpaid';
        $invoice->template = $request->input('template');
        $invoice->note = $request->input('note');
		$invoice->related_to = $request->input('related_to');

        if($invoice->related_to == 'contacts'){
			$invoice->related_id = $request->input('client_id');
			$invoice->client_id = $request->input('client_id');
			$invoice->converted_total = convert_currency(base_currency(), $invoice->client->currency, $invoice->grand_total);	
        }else if($invoice->related_to == 'projects'){
			$invoice->related_id = $request->input('project_id');
			$invoice->client_id = Project::find($invoice->related_id)->client_id;	
			$invoice->converted_total = convert_currency(base_currency(), $invoice->project->client->currency, $invoice->grand_total);
        }

        $invoice->company_id = $company_id;
	
        $invoice->save();

        //Save Invoice Item
        for($i=0; $i<count($request->product_id); $i++ ){
			$invoiceItem = new InvoiceItem();
			$invoiceItem->invoice_id = $invoice->id;
			$invoiceItem->item_id = $request->product_id[$i];
			$invoiceItem->description = $request->product_description[$i];
			$invoiceItem->quantity = $request->quantity[$i];
			$invoiceItem->unit_cost = $request->unit_cost[$i];
			$invoiceItem->discount = $request->discount[$i];
			$invoiceItem->tax_method = $request->tax_method[$i];
			$invoiceItem->tax_id = $request->tax_id[$i];
			$invoiceItem->tax_amount = $request->tax_amount[$i];
			$invoiceItem->sub_total = $request->sub_total[$i];
			$invoiceItem->company_id = $company_id;
			$invoiceItem->save();

			//Update Stock if Order Status is received
			if( has_feature('inventory_module') ){
				if($request->input('order_status') != 'Canceled'){
					$stock = Stock::where("product_id", $invoiceItem->item_id)->where("company_id",$company_id)->first();
					if(!empty($stock)){
						$stock->quantity =  $stock->quantity - $invoiceItem->quantity;
						$stock->company_id =  $company_id;
						$stock->save();
					}
				}
			}
        }
        
        //Increment Invoice Starting number
        increment_invoice_number();
		
		//Update Package limit
		update_package_limit('invoice_limit');

		if($invoice->client->user->id != null){
           Notification::send($invoice->client->user, new InvoiceCreated($invoice));
        }
		
		DB::commit();
        
		if(! $request->ajax()){
           return redirect('invoices/'.$invoice->id)->with('success', _lang('Invoice Created Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Invoice Created Sucessfully'),'data'=>$invoice]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $invoice = Invoice::where("id",$id)->where("company_id",company_id())->first();
		
		if(! $invoice){
			return back()->with('error', _lang('Sorry, Invoice not found !'));
		}
		
		$transactions = Transaction::where("invoice_id",$id)
								   ->where("company_id",company_id())->get();
		if(! $request->ajax()){
			$template = $invoice->template;

			if($invoice->template == ""){
				$template = "modern";
			}
            
            if(! file_exists(resource_path("views/backend/accounting/invoice/template/$template.blade.php"))){
            	$template = InvoiceTemplate::where('id',5)
            	                            ->where('company_id',company_id())
            	                            ->first();
            	                            
                return view("backend.accounting.invoice.template.custom",compact('invoice','transactions','template', 'id'));
            }

		    return view("backend.accounting.invoice.template.$template",compact('invoice','transactions','id'));
		}   
    }
	

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $invoice = Invoice::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.invoice.edit',compact('invoice','id'));
		}else{
           return view('backend.accounting.invoice.modal.edit',compact('invoice','id'));
		}  
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'invoice_number' => 'required|max:191',
            'related_to' => 'required',
            'client_id' => 'required_if:related_to,contacts',
            'project_id' => 'required_if:related_to,projects',
            'invoice_date' => 'required',
            'due_date' => 'required',
            'product_id' => 'required',
			'template' => 'required',
		],[
		   'product_id.required' => _lang('You must select at least one product or service')
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('invoices.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
	    DB::beginTransaction();
        $company_id = company_id();
		
        $invoice = Invoice::where("id",$id)->where("company_id",$company_id)->first();
		$previous_amount = $invoice->grand_total;
		$invoice->invoice_number = $request->input('invoice_number');
        $invoice->invoice_date = $request->input('invoice_date');
        $invoice->due_date = $request->input('due_date');
        $invoice->grand_total = $request->input('product_total');
        $invoice->tax_total = $request->input('tax_total');
		//$invoice->status = $request->input('status');
		$invoice->template = $request->input('template');
        $invoice->note = $request->input('note');
		$invoice->related_to = $request->input('related_to');

        if($invoice->related_to == 'contacts'){
			$invoice->related_id = $request->input('client_id');
			$invoice->client_id = $invoice->related_id;
			if($previous_amount != $invoice->grand_total){
			    $invoice->converted_total = convert_currency(base_currency(), $invoice->client->currency, $invoice->grand_total);
			}
			$invoice->client_id = $invoice->related_id;
        }else if($invoice->related_to == 'projects'){
			$invoice->related_id = $request->input('project_id');
			$invoice->client_id = Project::find($invoice->related_id)->client_id;
			if($previous_amount != $invoice->grand_total){
			    $invoice->converted_total = convert_currency(base_currency(), $invoice->project->client->currency, $invoice->grand_total);
			}		
        }

        $invoice->company_id = $company_id;
        $invoice->save();

        //Update Invoice item
		$invoiceItems = InvoiceItem::where("invoice_id",$id)->get();
		foreach($invoiceItems as $p_item){
			$invoiceItem = InvoiceItem::find($p_item->id);
			$invoiceItem->delete();
			$this->update_stock($p_item->item_id);
		}


		for($i = 0; $i<count($request->product_id); $i++ ){
			$invoiceItem = new InvoiceItem();
			$invoiceItem->invoice_id = $invoice->id;
			$invoiceItem->item_id = $request->product_id[$i];
			$invoiceItem->description = $request->product_description[$i];
			$invoiceItem->quantity = $request->quantity[$i];
			$invoiceItem->unit_cost = $request->unit_cost[$i];
			$invoiceItem->discount = $request->discount[$i];
			$invoiceItem->tax_method = $request->tax_method[$i];
			$invoiceItem->tax_id = $request->tax_id[$i];
			$invoiceItem->tax_amount = $request->tax_amount[$i];
			$invoiceItem->sub_total = $request->sub_total[$i];
			$invoiceItem->company_id = $company_id;
			$invoiceItem->save();

			$this->update_stock($request->product_id[$i]);
		}

		if($invoice->client->user->id != null){
           Notification::send($invoice->client->user, new InvoiceUpdated($invoice));
        }
		
		DB::commit();
		
		if(! $request->ajax()){
           return redirect('invoices/'.$invoice->id)->with('success', _lang('Invoice updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Invoice updated sucessfully'),'data'=>$invoice]);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		DB::beginTransaction();
		
        $invoice = Invoice::where("id",$id)->where("company_id",company_id());
        $invoice->delete();

        $invoiceItems = InvoiceItem::where("invoice_id",$id)->get();
        foreach($invoiceItems as $p_item){
			$invoiceItem = InvoiceItem::find($p_item->id);
			$invoiceItem->delete();
			$this->update_stock($p_item->item_id);
		}

		DB::commit();
		
        return redirect('invoices')->with('success',_lang('Invoice deleted sucessfully'));
    }
	
	public function create_payment(Request $request, $id)
    {
		$invoice = Invoice::where("id",$id)->where("company_id",company_id())->first();
		
		if($request->ajax()){
		   return view('backend.accounting.invoice.modal.create_payment',compact('invoice','id'));
		} 
	}
	
	public function store_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'invoice_id' => 'required',
			'account_id' => 'required',
			'chart_id' => 'required',
			'amount' => 'required|numeric',
			'payment_method_id' => 'required',
			'reference' => 'nullable|max:50',
			'attachment' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('income/create')
							->withErrors($validator)
							->withInput();
			}			
		}

		$attachment = "";
        if($request->hasfile('attachment'))
		{
		  $file = $request->file('attachment');
		  $attachment = time().$file->getClientOriginalName();
		  $file->move(public_path()."/uploads/transactions/", $attachment);
		}
			
		DB::beginTransaction();
		
        $company_id = company_id();
		
        $transaction= new Transaction();
	    $transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = $request->input('amount');
		$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
		$transaction->payer_payee_id = $request->input('client_id');
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->invoice_id = $request->input('invoice_id');
		$transaction->reference = $request->input('reference');
		$transaction->note = $request->input('note');
		$transaction->attachment = $attachment;
		$transaction->company_id = $company_id;
		
        $transaction->save();
		
		//Update Invoice Table
		$invoice = Invoice::where("id",$transaction->invoice_id)
						  ->where("company_id",$company_id)->first();
						  
		$invoice->paid = $invoice->paid + $transaction->base_amount;				
        if(round($invoice->paid,2) >= $invoice->grand_total){
			$invoice->status = 'Paid';
		}else if(round($invoice->paid,2) > 0 && (round($invoice->paid,2) < $invoice->grand_total)){
			$invoice->status = 'Partially_Paid';
		}
		$invoice->save();
		
		//Send Invoice Payment Confrimation to Client
		@ini_set('max_execution_time', 0);
	    @set_time_limit(0);
	    Overrider::load("Settings");
		$mail  = new \stdClass();
		$mail->subject = _lang('Invoice Payment');
		$mail->invoice = $invoice;
		$mail->transaction = $transaction;
		$mail->method = $transaction->payment_method->name;
		$mail->currency = currency();
		

		try{
			Mail::to($invoice->client->contact_email)->send(new InvoiceReceiptMail($mail));
		}catch (\Exception $e) {
			//Nothing
		}
		
		DB::commit();

		if( $request->ajax() ){
		   $request->session()->flash('success', _lang('Payment was made Sucessfully'));
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Payment was made Sucessfully'),'data'=>$transaction]);	                
		}
    }
	
	public function view_payment(Request $request, $invoice_id){

		$transactions = Transaction::where("invoice_id",$invoice_id)
								   ->where("company_id",company_id())->get();
	
	    if(! $request->ajax()){
		    return view('backend.accounting.invoice.view_payment',compact('transactions'));
		}else{
			return view('backend.accounting.invoice.modal.view_payment',compact('transactions'));
		} 
	}
	
	public function create_email(Request $request, $invoice_id)
    {
		$invoice = Invoice::where("id",$invoice_id)
						  ->where("company_id",company_id())->first();
		
		$client_email = $invoice->client->contact_email; 
		if($request->ajax()){
		    return view('backend.accounting.invoice.modal.send_email',compact('client_email','invoice'));
		} 
	}	
	
	public function send_email(Request $request)
    {
		@ini_set('max_execution_time', 0);
	    @set_time_limit(0);
	    Overrider::load("Settings");
		
		$validator = Validator::make($request->all(), [
			'email_subject' => 'required',
            'email_message' => 'required',
            'contact_email' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}			
		}
	   
		//Send email
		$subject = $request->input("email_subject");
		$message = $request->input("email_message");
		$contact_email = $request->input("contact_email");
		
		$contact = Contact::where('contact_email',$contact_email)->first();
		$invoice = Invoice::where('id',$request->invoice_id)
						  ->where('company_id', company_id())
						  ->first();
						  
		$currency = currency();
		
		if( $contact ){
			//Replace Paremeter
			$replace = array(
				'{customer_name}'	=> $contact->contact_name,
				'{invoice_no}'		=> $invoice->invoice_number,
				'{invoice_date}' 	=> date('d M,Y', strtotime($invoice->invoice_date)),
				'{due_date}' 		=> date('d M,Y', strtotime($invoice->due_date)),
				'{payment_status}' 	=> _dlang(str_replace('_',' ',$invoice->status)),
				'{grand_total}' 	=> decimalPlace($invoice->grand_total, $currency),
				'{amount_due}' 		=> decimalPlace(($invoice->grand_total - $invoice->paid), $currency),
				'{total_paid}' 		=> decimalPlace($invoice->paid, $currency),
				'{invoice_link}' 	=> url('client/view_invoice/'.md5($invoice->id)),
			);
			
		}
		
		$mail  = new \stdClass();
		$mail->subject = $subject;
		$mail->body = process_string($replace, $message);
		
		try{
			Mail::to($contact_email)->send(new GeneralMail($mail));
		}catch (\Exception $e) {
			if(! $request->ajax()){
			   return back()->with('error', _lang('Sorry, Error Occured !'));
			}else{
			   return response()->json(['result'=>'error','message'=>_lang('Sorry, Error Occured !')]);
			}
		}
		
        if(! $request->ajax()){
           return back()->with('success', _lang('Email Send Sucessfully'));
        }else{
		   return response()->json(['result'=>'success', 'action'=>'update', 'message'=>_lang('Email Send Sucessfully'),'data'=>$contact]);
		}
    }
	
	public function mark_as_cancelled($id){
		$invoice = Invoice::where("id", $id)->where("company_id",company_id())->first();
		if($invoice){
			if($invoice->status == 'Unpaid'){
				$invoice->status = 'Canceled';
				$invoice->save();
				return back()->with('success', _lang('Invoice Marked as Canceled'));
			}
		}
		return back();
	}

	private function update_stock($product_id){
		$company_id = company_id();
		$purchase = DB::table('purchase_order_items')->where('product_id',$product_id)
		                                             ->where('company_id',$company_id)
													 ->sum('quantity');

		$purchaseReturn = DB::table('purchase_return_items')->where('product_id',$product_id)
		                                             ->where('company_id',$company_id)
													 ->sum('quantity');

		$sales = DB::table('invoice_items')->where('item_id',$product_id)
		                                   ->where('company_id',$company_id)
										   ->sum('quantity');
										   
		$salesReturn = DB::table('sales_return_items')->where('product_id',$product_id)
													  ->where('company_id',$company_id)
												      ->sum('quantity');								   
		
		//Update Stock
		$stock = Stock::where("product_id", $product_id)->where("company_id",company_id())->first();
		if($stock){
			$stock->quantity =  ($purchase + $salesReturn) - ($sales + $purchaseReturn);
			$stock->save();
		}
		
	}
	
}
