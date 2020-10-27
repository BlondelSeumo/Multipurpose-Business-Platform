<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use App\PurchaseOrderItem;
use App\Transaction;
use App\Stock;
use App\Tax;
use Validator;
use Illuminate\Validation\Rule;
use DB;
use PDF;

class PurchaseController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'inventory_module' ) ){
                    if( ! $request->ajax()){
						return redirect('membership/extend')->with('message', _lang('Your Current package not support this feature. You can upgrade your package !'));
                    }else{
						return response()->json(['result'=>'error','message'=>_lang('Sorry, This feature is not available in your current subscription !')]);
					}
                }
            }

            return $next($request);
        });
		
		date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka')));	
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchases = Purchase::where("company_id",company_id())
							 ->orderBy("id","desc")->get();
        return view('backend.accounting.purchase_order.list',compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.purchase_order.create');
		}else{
           return view('backend.accounting.purchase_order.modal.create');
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
			'order_date' => 'required',
			'supplier_id' => 'required',
			'order_status' => 'required',
			'order_discount' => 'nullable|numeric',
			'shipping_cost' => 'nullable|numeric',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('purchase_orders/create')
							->withErrors($validator)
							->withInput();
			}			
		}

		DB::beginTransaction();
		
		$company_id = company_id();
			
		$attachemnt = "";
	    if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}
		
        $purchase= new Purchase();
	    $purchase->order_date = $request->input('order_date');
		$purchase->supplier_id = $request->input('supplier_id');
		$purchase->order_status = $request->input('order_status');

		if($request->input('order_tax_id') != ""){
			$purchase->order_tax_id = $request->input('order_tax_id');
			$tax = Tax::find($purchase->order_tax_id);
			if($tax->type == "percent"){
				$purchase->order_tax =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$purchase->order_tax = $tax->rate;
			}		
		}

		$purchase->order_discount = $request->input('order_discount');
		$purchase->shipping_cost = $request->input('shipping_cost');
		$purchase->product_total = $request->input('product_total');
		$purchase->grand_total = ($purchase->product_total + $purchase->shipping_cost + $purchase->order_tax) - $purchase->discount;
		$purchase->paid = 0;
		$purchase->payment_status = 0;
		$purchase->attachemnt = $attachemnt;
		$purchase->note = $request->input('note');
		$purchase->company_id = $company_id;
	
		$purchase->save();
		

		//Save Purcahse item
		for($i=0; $i<count($request->product_id); $i++ ){
			$purchaseItem = new PurchaseOrderItem();
			$purchaseItem->purchase_order_id = $purchase->id;
			$purchaseItem->product_id = $request->product_id[$i];
			$purchaseItem->description = $request->product_description[$i];
			$purchaseItem->quantity = $request->quantity[$i];
			$purchaseItem->unit_cost = $request->unit_cost[$i];
			$purchaseItem->discount = $request->discount[$i];
			$purchaseItem->tax_method = $request->tax_method[$i];
			$purchaseItem->tax_id = $request->tax_id[$i];
			$purchaseItem->tax_amount = $request->tax_amount[$i];
			$purchaseItem->sub_total = $request->sub_total[$i];
			$purchaseItem->company_id = $company_id;
			$purchaseItem->save();

			//Update Stock if Order Status is received
			if($request->input('order_status') == '3'){
				$stock = Stock::where("product_id",$purchaseItem->product_id)->where("company_id",$company_id)->first();
				$stock->quantity =  $stock->quantity + $purchaseItem->quantity;
				$stock->company_id =  $company_id;
				$stock->save();
			}
		}
		DB::commit();

        
		if(! $request->ajax()){
           return redirect('purchase_orders/'.$purchase->id)->with('success', _lang('Purchase Order Created Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Purchase Order Created Sucessfully'),'data'=>$purchase]);
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
        $purchase = Purchase::where("id",$id)->where("company_id",company_id())->first();
		$transactions = Transaction::where("purchase_id",$id)
								   ->where("company_id",company_id())->get();
								   
		if(! $request->ajax()){
		    return view('backend.accounting.purchase_order.view',compact('purchase','transactions','id'));
		}else{
			return view('backend.accounting.purchase_order.modal.view',compact('purchase','transactions','id'));
		} 
        
    }
	
	/**
     * Generate PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	public function download_pdf(Request $request, $id)
    {
		@ini_set('max_execution_time', 0);
	    @set_time_limit(0);
		
		$data = array();
		$data['purchase'] = Purchase::where("id",$id)->where("company_id",company_id())->first();
		$data['transactions'] = Transaction::where("purchase_id",$id)
								   ->where("company_id",company_id())->get();
			
		$pdf = PDF::loadView("backend.accounting.purchase_order.pdf_export", $data);
		$pdf->setWarnings(false);
		
		//return $pdf->stream();
		return $pdf->download("purchase_order_{$data['purchase']->id}.pdf");

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $purchase = Purchase::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.purchase_order.edit',compact('purchase','id'));
		}else{
           return view('backend.accounting.purchase_order.modal.edit',compact('purchase','id'));
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
			'order_date' => 'required',
			'supplier_id' => 'required',
			'order_status' => 'required',
			'order_discount' => 'nullable|numeric',
			'shipping_cost' => 'nullable|numeric',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('purchase_orders.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}

		DB::beginTransaction();
		
		$company_id = company_id();
	
        if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}	
		
		$purchase = Purchase::where("id",$id)->where("company_id",$company_id)->first();

		$previous_status = $purchase->order_status;
		
		$purchase->order_date = $request->input('order_date');
		$purchase->supplier_id = $request->input('supplier_id');
		$purchase->order_status = $request->input('order_status');

		if($request->input('order_tax_id') != ''){
			$purchase->order_tax_id = $request->input('order_tax_id');
			$tax = Tax::find($purchase->order_tax_id);
			if($tax->type == "percent"){
				$purchase->order_tax =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$purchase->order_tax = $tax->rate;
			}		
		}else{
			$purchase->order_tax_id = null;
			$purchase->order_tax = 0;
		}

		$purchase->order_discount = $request->input('order_discount');
		$purchase->shipping_cost = $request->input('shipping_cost');
		$purchase->product_total = $request->input('product_total');
		$purchase->grand_total = ($purchase->product_total + $purchase->shipping_cost + $purchase->order_tax) - $purchase->discount;

		$purchase->payment_status = $request->input('payment_status');
        				
	    if(round($purchase->paid,2) < $purchase->grand_total){
			$purchase->payment_status = 0;
		}
		
		if($request->hasfile('attachemnt')){
			$purchase->attachemnt = $attachemnt;
		}
		$purchase->note = $request->input('note');
		$purchase->company_id = $company_id;
	
		$purchase->save();

		//Update Purcahse item
		$purchaseItems = PurchaseOrderItem::where("purchase_order_id",$id)->get();
		foreach($purchaseItems as $p_item){
			$returnItem = PurchaseOrderItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}


		for($i=0; $i<count($request->product_id); $i++ ){
			$purchaseItem = new PurchaseOrderItem();
			$purchaseItem->purchase_order_id = $purchase->id;
			$purchaseItem->product_id = $request->product_id[$i];
			$purchaseItem->description = $request->product_description[$i];
			$purchaseItem->quantity = $request->quantity[$i];
			$purchaseItem->unit_cost = $request->unit_cost[$i];
			$purchaseItem->discount = $request->discount[$i];
			$purchaseItem->tax_method = $request->tax_method[$i];
			$purchaseItem->tax_id = $request->tax_id[$i];
			$purchaseItem->tax_amount = $request->tax_amount[$i];
			$purchaseItem->sub_total = $request->sub_total[$i];
			$purchaseItem->company_id = $company_id;
			$purchaseItem->save();

			//Update Stock if Order Status is received
			if($request->input('order_status') == '3'){
				$this->update_stock($request->product_id[$i]);
			}

		}
		
		DB::commit();
		
		if(! $request->ajax()){
           return redirect('purchase_orders/'.$purchase->id)->with('success', _lang('Purchase Order Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Purchase Order Updated Sucessfully'),'data'=>$purchase]);
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
		
		$purchase = Purchase::where("id",$id)
							->where("company_id",company_id());
		$purchase->delete();
		
		//Remove Purchase Item
		$purchaseItems = PurchaseOrderItem::where("purchase_order_id",$id)->get();
		foreach($purchaseItems as $p_item){
			$returnItem = PurchaseOrderItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}
		
		DB::commit();

        return redirect('purchase_orders')->with('success',_lang('Deleted Sucessfully'));
	}
	

	public function create_payment(Request $request, $id)
    {
		$purchase = Purchase::where("id",$id)->where("company_id",company_id())->first();
		
		if($request->ajax()){
		   return view('backend.accounting.purchase_order.modal.create_payment',compact('purchase','id'));
		} 
	}
	
	public function store_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
			'purchase_id' => 'required',
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
				return redirect('expense/create')
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
			
        $company_id = company_id();
		
        $transaction= new Transaction();
	    $transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'expense';
		$transaction->dr_cr = 'dr';
		$transaction->amount = $request->input('amount');
		$transaction->base_amount = convert_currency($transaction->account->account_currency, base_currency(), $transaction->amount);
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->purchase_id = $request->input('purchase_id');
		$transaction->reference = $request->input('reference');
		$transaction->note = $request->input('note');
		$transaction->attachment = $attachment;
		$transaction->company_id = $company_id;
		
        $transaction->save();
		
		//Update Purchase Order Table
		$purchase = Purchase::where("id",$transaction->purchase_id)
							->where("company_id",$company_id)->first();
		$purchase->paid = $purchase->paid + $transaction->base_amount;				
        if(round($purchase->paid,2) >= $purchase->grand_total){
			$purchase->payment_status = 1;
		}
		$purchase->save();
		

		if( $request->ajax() ){
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Payment was made Sucessfully'),'data'=>$transaction]);
		}
    }
	
	
	public function view_payment(Request $request, $purchase_id){

		$transactions = Transaction::where("purchase_id",$purchase_id)
								  ->where("company_id",company_id())->get();
	
	    if(! $request->ajax()){
		    return view('backend.accounting.purchase_order.view_payment',compact('transactions'));
		}else{
			return view('backend.accounting.purchase_order.modal.view_payment',compact('transactions'));
		} 
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
		$stock->quantity =  ($purchase + $salesReturn) - ($sales + $purchaseReturn);
		$stock->save();
	}
		
	
}
