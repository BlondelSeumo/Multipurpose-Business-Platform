<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quotation;
use App\QuotationItem;
use App\CompanySetting;
use App\Invoice;
use App\InvoiceItem;
use App\Stock;
use App\Contact;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralMail;
use App\Utilities\Overrider;
use Carbon\Carbon;
use DataTables;
use DB;
use PDF;

class QuotationController extends Controller
{
	
	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka')));	

        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'quotation_limit' ) ){
                    return redirect('membership/extend')->with('message',_lang('Your Current package not support this feature. You can upgrade your package !'));
                }

                // If request is create/store
                $route_name = \Request::route()->getName();
                if( $route_name == 'quotations.store'){
                   if( ! has_feature_limit( 'quotation_limit' ) ){
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
        return view('backend.accounting.quotation.list');
    }
	
	public function get_table_data(){

		$currency = currency();		
		$company_id =company_id();  

		$leads = DB::table('leads')
	                ->select('id','name as contact_name',DB::raw('"leads" as type'))
	                ->where('company_id', $company_id);

		$all_contacts = DB::table('contacts')
	                  ->select('id','contact_name',DB::raw('"contacts" as type'))
	                  ->where('company_id', $company_id)
	                  ->union($leads);

		$quotations = Quotation::joinSub($all_contacts, 'all_contacts', function ($join) {
						            $join->on('quotations.related_id', '=', 'all_contacts.id')
						                 ->on('quotations.related_to', '=', 'all_contacts.type');
						        })
		                       ->select("quotations.*","all_contacts.contact_name","all_contacts.id as contact_id")
							   ->where('quotations.company_id',$company_id)
	                           ->orderBy('quotations.id','desc');	   

		return Datatables::eloquent($quotations)	
						->addColumn('contact_name', function ($quotation) {
							if($quotation->related_to == 'contacts'){
								return '<a href="'.action('ContactController@show', $quotation->related_id).'">'.$quotation->contact_name.' <span class="text-muted small">('._lang('Customer').')</span></a>';
							}
							return '<a href="'.action('LeadController@show', $quotation->related_id).'" data-title="'. _lang('View Lead Details') .'" class="ajax-modal">'.$quotation->contact_name.' <span class="text-muted small">('._lang('Lead').')</span></a>';
						})
						->filterColumn('contact_name', function($query, $keyword) {
		                    $sql = "all_contacts.contact_name  like ?";
		                    $query->whereRaw($sql, ["%{$keyword}%"]);
		                })
		                ->editColumn('quotation_number', function ($quotation) {
							return '<a href="'. action('QuotationController@show', $quotation->id) .'">'.$quotation->quotation_number.'</a>';
						})
						->editColumn('quotation_date', function ($quotation) {
							$date_format = get_company_option('date_format','Y-m-d');
							return date($date_format, strtotime($quotation->quotation_date));
						})
						->editColumn('grand_total', function ($quotation) use ($currency){
							if($quotation->related_to == 'contacts'){
							    $acc_currency = currency($quotation->client->currency);
							}else{
								$acc_currency = currency($quotation->lead->currency);
							}
							if($acc_currency != $currency){
								return "<span class='float-right'>".decimalPlace($quotation->grand_total, $currency)."</span><br>
										<span class='float-right'><b>".decimalPlace($quotation->converted_total, $currency)."</b></span>";
							}else{
								return "<span class='float-right'>".decimalPlace($quotation->grand_total, $currency)."</span>";
							}
						})
						->addColumn('action', function ($quotation) {
								if($quotation->related_to == 'contacts'){
									return '<div class="dropdown">'
											.'<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">'._lang('Action')
											.'&nbsp;<i class="fas fa-angle-down"></i></button>'
											.'<div class="dropdown-menu">'
												.'<a class="dropdown-item" href="'. action('QuotationController@edit', $quotation->id) .'"><i class="fas fa-edit"></i> '. _lang('Edit') .'</a></li>'
												.'<a class="dropdown-item" href="'. action('QuotationController@show', $quotation->id) .'"><i class="fas fa-eye"></i> '. _lang('View') .'</a></li>'
												.'<a class="dropdown-item" href="'. action('QuotationController@convert_invoice', $quotation->id) .'"><i class="fas fa-exchange-alt"></i> '. _lang('Convert to Invoice') .'</a></li>'
													.'<form action="'. action('QuotationController@destroy', $quotation['id']) .'" method="post">'								
														.csrf_field()
														.'<input name="_method" type="hidden" value="DELETE">'
														.'<button class="button-link btn-remove" type="submit"><i class="fas fa-recycle"></i> '._lang('Delete') .'</button>'
													.'</form>'	
												.'</div>'
											.'</div>';
								}else{
									return '<div class="dropdown">'
											.'<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">'._lang('Action')
											.'&nbsp;<i class="fas fa-angle-down"></i></button>'
											.'<div class="dropdown-menu">'
												.'<a class="dropdown-item" href="'. action('QuotationController@edit', $quotation->id) .'"><i class="fas fa-edit"></i> '. _lang('Edit') .'</a></li>'
												.'<a class="dropdown-item" href="'. action('QuotationController@show', $quotation->id) .'"><i class="fas fa-eye"></i> '. _lang('View') .'</a></li>'
													.'<form action="'. action('QuotationController@destroy', $quotation['id']) .'" method="post">'								
														.csrf_field()
														.'<input name="_method" type="hidden" value="DELETE">'
														.'<button class="button-link btn-remove" type="submit"><i class="fas fa-recycle"></i> '._lang('Delete') .'</button>'
													.'</form>'	
												.'</div>'
											.'</div>';
								}		
						})
						->setRowId(function ($invoice) {
							return "row_".$invoice->id;
						})
						->rawColumns(['grand_total','action','contact_name','quotation_number'])
						->toJson();							    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.quotation.create');
		}else{
           return view('backend.accounting.quotation.modal.create');
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
			'quotation_number' => 'required|max:191',
            'related_to' => 'required',
            'client_id' => 'required_if:related_to,contacts',
            'lead_id' => 'required_if:related_to,leads',
            'quotation_date' => 'required',
            'product_id' => 'required',
            'template' => 'required',
		],
		[
		   'product_id.required' => _lang('You must select at least one product or service')
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('quotations/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		DB::beginTransaction();	
		
	    $company_id = company_id();
		
        $quotation= new Quotation();
	    $quotation->quotation_number = $request->input('quotation_number');
        $quotation->quotation_date = $request->input('quotation_date');
        $quotation->template = $request->input('template');
        $quotation->grand_total = $request->input('product_total');
        $quotation->tax_total = $request->input('tax_total');
        $quotation->note = $request->input('note');
		$quotation->related_to = $request->related_to;
		
        if($request->related_to == 'contacts'){
        	$quotation->related_id = $request->client_id;
        	$quotation->converted_total = convert_currency(base_currency(), $quotation->client->currency, $quotation->grand_total);
			
        }else{
        	$quotation->related_id = $request->lead_id;
        	$quotation->converted_total = convert_currency(base_currency(), $quotation->lead->currency, $quotation->grand_total);
        }
        
        $quotation->company_id = $company_id;
	
        $quotation->save();

        //Save quotation Item
        for($i=0; $i<count($request->product_id); $i++ ){
			$quotationItem = new quotationItem();
			$quotationItem->quotation_id = $quotation->id;
			$quotationItem->item_id = $request->product_id[$i];
			$quotationItem->description = $request->product_description[$i];
			$quotationItem->quantity = $request->quantity[$i];
			$quotationItem->unit_cost = $request->unit_cost[$i];
			$quotationItem->discount = $request->discount[$i];
			$quotationItem->tax_method = $request->tax_method[$i];
			$quotationItem->tax_id = $request->tax_id[$i];
			$quotationItem->tax_amount = $request->tax_amount[$i];
			$quotationItem->sub_total = $request->sub_total[$i];
			$quotationItem->save();

        }
        
        //Increment quotation Starting number
        $data = array();
        $data['value'] = $request->quotation_starting_number + 1; 
        $data['company_id'] = $company_id; 
        $data['updated_at'] = Carbon::now();
        
        if(CompanySetting::where('name', "quotation_starting")->where("company_id",$company_id)->exists()){				
           CompanySetting::where('name','quotation_starting')
                         ->where("company_id",$company_id)
                         ->update($data);			
        }else{
           $data['name'] = 'quotation_starting'; 
           $data['created_at'] = Carbon::now();
           CompanySetting::insert($data); 
        }
		
		//Update Package limit
		update_package_limit('quotation_limit');
		
		DB::commit();
        
		if(! $request->ajax()){
           return redirect('quotations/'.$quotation->id)->with('success', _lang('Quotation Created Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Quotation Created Sucessfully'),'data'=>$quotation]);
		}
        
   }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $quotation = Quotation::where("id",$id)->where("company_id",company_id())->first();

		if(! $request->ajax()){
			$template = $quotation->template;
			if($template == ""){
				$template = "modern";
			}
			
		    return view("backend.accounting.quotation.template.$template",compact('quotation','id'));
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
        $quotation = Quotation::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.quotation.edit',compact('quotation','id'));
		}else{
           return view('backend.accounting.quotation.modal.edit',compact('quotation','id'));
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
			'quotation_number' => 'required|max:191',
            'related_to' => 'required',
            'client_id' => 'required_if:related_to,customer',
            'lead_id' => 'required_if:related_to,lead',
            'quotation_date' => 'required',
            'product_id' => 'required',
			'template' => 'required',
		],
		[
		   'product_id.required' => _lang('You must select at least one product or service')
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('quotations.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
		DB::beginTransaction();
		
        $company_id = company_id();
		
        $quotation = Quotation::where("id",$id)->where("company_id",$company_id)->first();
		$previous_amount = $quotation->grand_total;
		$quotation->quotation_number = $request->input('quotation_number');
        $quotation->quotation_date = $request->input('quotation_date');
        $quotation->template = $request->input('template');
	    $quotation->grand_total = $request->input('product_total');
		$quotation->tax_total = $request->input('tax_total');
        $quotation->note = $request->input('note');

        if($quotation->related_to == 'contacts'){
			$quotation->related_id = $request->input('client_id');
			if($previous_amount != $quotation->grand_total){
			    $quotation->converted_total = convert_currency(base_currency(), $quotation->client->currency, $quotation->grand_total);
			}
        }else{
			$quotation->related_id = $request->input('lead_id');
			if($previous_amount != $quotation->grand_total){
			    $quotation->converted_total = convert_currency(base_currency(), $quotation->lead->currency, $quotation->grand_total);
			}
        }
        
        $quotation->company_id = $company_id;
	
        $quotation->save();

        //Update quotation item
		$quotationItem = QuotationItem::where("quotation_id",$id);
        $quotationItem->delete();

		for($i=0; $i<count($request->product_id); $i++ ){
			$quotationItem = new quotationItem();
			$quotationItem->quotation_id = $quotation->id;
			$quotationItem->item_id = $request->product_id[$i];
			$quotationItem->description = $request->product_description[$i];
			$quotationItem->quantity = $request->quantity[$i];
			$quotationItem->unit_cost = $request->unit_cost[$i];
			$quotationItem->tax_method = $request->tax_method[$i];
			$quotationItem->discount = $request->discount[$i];
			$quotationItem->tax_id = $request->tax_id[$i];
			$quotationItem->tax_amount = $request->tax_amount[$i];
			$quotationItem->sub_total = $request->sub_total[$i];
			$quotationItem->save();
		}
		
		DB::commit();
		
		if(! $request->ajax()){
           return redirect('quotations/'.$quotation->id)->with('success', _lang('Quotation updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Quotation updated sucessfully'),'data'=>$quotation]);
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
		
        $quotation = Quotation::where("id",$id)->where("company_id",company_id());
        $quotation->delete();

        $quotationItem = QuotationItem::where("quotation_id",$id);
        $quotationItem->delete();
		
		DB::commit();

        return redirect('quotations')->with('success',_lang('Quotation Removed Sucessfully'));
    }
	
	public function convert_invoice($quotation_id){
		@ini_set('max_execution_time', 0);
	    @set_time_limit(0);
		
		DB::beginTransaction();
		
		$company_id = company_id();
		$quotation = Quotation::where("id",$quotation_id)
							  ->where("company_id",$company_id)->first();
							  
		$invoice = new Invoice();
	    $invoice->invoice_number = get_company_option('invoice_prefix').get_company_option('invoice_starting');
        $invoice->invoice_date = date('Y-m-d');
        $invoice->due_date = date('Y-m-d');
        $invoice->grand_total = $quotation->grand_total;
        $invoice->tax_total = $quotation->tax_total;
        $invoice->paid = 0;
        $invoice->status = 'Unpaid';
        $invoice->note = $quotation->note;
        $invoice->template = $quotation->template;
		$invoice->related_to = 'contacts';

        if($invoice->related_to == 'contacts'){
			$invoice->related_id = $quotation->related_id;
			$invoice->client_id = $quotation->related_id;
			$invoice->converted_total = $quotation->converted_total;	
        }

        $invoice->company_id = $company_id;
	
        $invoice->save();
		

        //Save Invoice Item
        foreach($quotation->quotation_items as $quotation_item){
			$invoiceItem = new InvoiceItem();
			$invoiceItem->invoice_id = $invoice->id;
			$invoiceItem->item_id = $quotation_item->item_id;
			$invoiceItem->quantity = $quotation_item->quantity;
			$invoiceItem->unit_cost = $quotation_item->unit_cost;
			$invoiceItem->discount = $quotation_item->discount;
			$invoiceItem->tax_method = $quotation_item->tax_method;
			$invoiceItem->tax_id = $quotation_item->tax_id;
			$invoiceItem->tax_amount = $quotation_item->tax_amount;
			$invoiceItem->sub_total = $quotation_item->sub_total;
			$invoiceItem->company_id = $company_id;
			$invoiceItem->save();

			//Update Stock
			$stock = Stock::where("product_id",$invoiceItem->item_id)->where("company_id",$company_id)->first();
			if(!empty($stock)){
				$stock->quantity =  $stock->quantity - $invoiceItem->quantity;
				$stock->company_id =  $company_id;
				$stock->save();
			}
			
        }
        
        //Increment Invoice Starting number
        increment_invoice_number();
		
		//Remove Existing Quotation
		$quotation = Quotation::where("id",$quotation_id)->where("company_id",company_id());
        $quotation->delete();

        $quotationItem = QuotationItem::where("quotation_id",$quotation_id);
        $quotationItem->delete();
		
		DB::commit();
		
		
        return redirect('invoices/'.$invoice->id)->with('success', _lang('Quotation Converted Sucessfully'));
        
		
	}
	
	public function create_email(Request $request, $quotation_id)
    {
		$quotation = Quotation::where("id",$quotation_id)
						  ->where("company_id",company_id())->first();
		
		$client_email = $quotation->client->contact_email; 
		if($request->ajax()){
		    return view('backend.accounting.quotation.modal.send_email', compact('client_email','quotation'));
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
		$quotation = Quotation::where('id',$request->quotation_id)
							  ->where('company_id', company_id())
							  ->first();
						  
		$currency = currency();
		
		if( $contact ){
			//Replace Paremeter
			$replace = array(
				'{customer_name}'	=> $contact->contact_name,
				'{quotation_no}'	=> $quotation->quotation_number,
				'{quotation_date}' 	=> date('d M,Y', strtotime($quotation->quotation_date)),
				'{grand_total}' 	=> decimalPlace($quotation->grand_total, $currency),
				'{quotation_link}' 	=> url('client/view_quotation/'.md5($quotation->id)),
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
		   return response()->json(['result'=>'success', 'message'=>_lang('Email Send Sucessfully')]);
		}
    }
}
