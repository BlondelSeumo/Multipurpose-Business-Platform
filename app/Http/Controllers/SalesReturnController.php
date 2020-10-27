<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesReturn;
use App\SalesReturnItem;
use App\Stock;
use App\Tax;
use Validator;
use DB;
use Illuminate\Validation\Rule;

class SalesReturnController extends Controller
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
        $sales_returns = SalesReturn::where("company_id",company_id())
							 	    ->orderBy("id","desc")->get();
        return view('backend.accounting.sales_return.list',compact('sales_returns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.sales_return.create');
		}else{
           return view('backend.accounting.sales_return.modal.create');
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
			'return_date' => 'required',
			'customer_id' => 'required',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('sales_returns/create')
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
		

        $salesReturn= new SalesReturn();
	    $salesReturn->return_date = $request->input('return_date');
		$salesReturn->customer_id = $request->input('customer_id');
		
		if($request->input('return_tax_id') != ''){
			$salesReturn->tax_id = $request->input('return_tax_id');
			$tax = Tax::find($salesReturn->tax_id);
			if($tax->type == "percent"){
				$salesReturn->tax_amount =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$salesReturn->tax_amount = $tax->rate;
			}		
		}

		$salesReturn->product_total = $request->input('product_total');
		$salesReturn->grand_total = ($salesReturn->product_total + $salesReturn->tax_amount);
		$salesReturn->converted_total = convert_currency(base_currency(), $salesReturn->customer->currency, $salesReturn->grand_total);
		$salesReturn->attachemnt = $attachemnt;
		$salesReturn->note = $request->input('note');
		$salesReturn->company_id = $company_id;
	
		$salesReturn->save();
		

		//Save Sales Return item
		for($i = 0; $i<count($request->product_id); $i++ ){
			$salesReturnItem = new SalesReturnItem();
			$salesReturnItem->sales_return_id = $salesReturn->id;
			$salesReturnItem->product_id = $request->product_id[$i];
			$salesReturnItem->description = $request->product_description[$i];
			$salesReturnItem->quantity = $request->quantity[$i];
			$salesReturnItem->unit_cost = $request->unit_cost[$i];
			$salesReturnItem->discount = $request->discount[$i];
			$salesReturnItem->tax_method = $request->tax_method[$i];
			$salesReturnItem->tax_id = $request->tax_id[$i];
			$salesReturnItem->tax_amount = $request->tax_amount[$i];
			$salesReturnItem->sub_total = $request->sub_total[$i];
			$salesReturnItem->company_id = $company_id;
			$salesReturnItem->save();

			//Update Stock
			$stock = Stock::where("product_id", $salesReturnItem->product_id)
						  ->where("company_id",$company_id)->first();
			$stock->quantity = $stock->quantity + $salesReturnItem->quantity;
			$stock->company_id = $company_id;
			$stock->save();
		}
		
		DB::commit();

        
		if(! $request->ajax()){
           return redirect('sales_returns/create')->with('success', _lang('Sales Returned Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Sales Returned Sucessfully'),'data'=>$purchase]);
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
        $sales = SalesReturn::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.sales_return.view',compact('sales','id'));
		}else{
			return view('backend.accounting.sales_return.modal.view',compact('sales','id'));
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
        $sales = SalesReturn::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.sales_return.edit',compact('sales','id'));
		}else{
           return view('backend.accounting.sales_return.modal.edit',compact('sales','id'));
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
			'return_date' => 'required',
			'customer_id' => 'required',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('sales_returns.edit', $id)
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
		

        $salesReturn = SalesReturn::where("id",$id)->where("company_id",$company_id)->first();
	    $previous_amount = $salesReturn->grand_total;
		$salesReturn->return_date = $request->input('return_date');
		$salesReturn->customer_id = $request->input('customer_id');

		if($request->input('return_tax_id') != ""){
			$salesReturn->tax_id = $request->input('return_tax_id');
			$tax = Tax::find($salesReturn->tax_id);
			if($tax->type == "percent"){
				$salesReturn->tax_amount =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$salesReturn->tax_amount = $tax->rate;
			}		
		}else{
			$salesReturn->tax_id = null;
			$salesReturn->tax_amount = 0;
		}

		$salesReturn->product_total = $request->input('product_total');
		$salesReturn->grand_total = ($salesReturn->product_total + $salesReturn->tax_amount);
		if($previous_amount != $salesReturn->grand_total){
			$salesReturn->converted_total = convert_currency(base_currency(), $salesReturn->customer->currency, $salesReturn->grand_total);
		}
		$salesReturn->attachemnt = $attachemnt;
		$salesReturn->note = $request->input('note');
		$salesReturn->company_id = $company_id;
	
		$salesReturn->save();


		//Remove Previous Purcahse item
		$previous_items = SalesReturnItem::where("sales_return_id",$id)->get();
		foreach($previous_items as $p_item){
			$returnItem = SalesReturnItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}


		for($i=0; $i<count($request->product_id); $i++ ){
			$returnItem = new SalesReturnItem();
			$returnItem->sales_return_id = $salesReturn->id;
			$returnItem->product_id = $request->product_id[$i];
			$returnItem->description = $request->product_description[$i];
			$returnItem->quantity = $request->quantity[$i];
			$returnItem->unit_cost = $request->unit_cost[$i];
			$returnItem->discount = $request->discount[$i];
			$returnItem->tax_method = $request->tax_method[$i];
			$returnItem->tax_id = $request->tax_id[$i];
			$returnItem->tax_amount = $request->tax_amount[$i];
			$returnItem->sub_total = $request->sub_total[$i];
			$returnItem->company_id = $company_id;
			$returnItem->save();

			$this->update_stock($request->product_id[$i]);

		}
		
		DB::commit();

				
		if(! $request->ajax()){
           return redirect('sales_returns')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$purchase]);
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
		
        $salesReturn = SalesReturn::where("id",$id)
							      ->where("company_id",company_id());
		$salesReturn->delete();
		
		//Remove Sales Return Items
		$salesReturnItems = SalesReturnItem::where("sales_return_id",$id)->get();
		foreach($salesReturnItems as $p_item){
			$returnItem = SalesReturnItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}
		
		DB::commit();

        return redirect('sales_returns')->with('success',_lang('Deleted Sucessfully'));
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
