<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseReturn;
use App\PurchaseReturnItem;
use App\Transaction;
use App\Stock;
use App\Tax;
use Validator;
use DB;
use Illuminate\Validation\Rule;

class PurchaseReturnController extends Controller
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
        $purchase_returns = PurchaseReturn::where("company_id",company_id())
							 	   ->orderBy("id","desc")->get();
        return view('backend.accounting.purchase_return.list',compact('purchase_returns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.purchase_return.create');
		}else{
           return view('backend.accounting.purchase_return.modal.create');
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
			'account_id' => 'required',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('purchase_returns/create')
							->withErrors($validator)
							->withInput();
			}			
		}

		DB::beginTransaction();
		$company_id = company_id();
			
		$attachemnt = '';
	    if($request->hasfile('attachemnt'))
		{
			$file = $request->file('attachemnt');
			$attachemnt = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/attachments/", $attachemnt);
		}
		

        $purchaseReturn= new PurchaseReturn();
	    $purchaseReturn->return_date = $request->input('return_date');
		$purchaseReturn->supplier_id = $request->input('supplier_id');
		$purchaseReturn->account_id = $request->input('account_id');
		$purchaseReturn->chart_id = $request->input('chart_id');
		$purchaseReturn->payment_method_id = $request->input('payment_method_id');

		if($request->input('return_tax_id') != ""){
			$purchaseReturn->tax_id = $request->input('return_tax_id');
			$tax = Tax::find($purchaseReturn->tax_id);
			if($tax->type == "percent"){
				$purchaseReturn->tax_amount =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$purchaseReturn->tax_amount = $tax->rate;
			}		
		}

		$purchaseReturn->product_total = $request->input('product_total');
		$purchaseReturn->grand_total = ($purchaseReturn->product_total + $purchaseReturn->tax_amount);
		$purchaseReturn->attachemnt = $attachemnt;
		$purchaseReturn->note = $request->input('note');
		$purchaseReturn->company_id = $company_id;
	
		$purchaseReturn->save();
		

		//Save Purcahse Return item
		for($i = 0; $i<count($request->product_id); $i++ ){
			$purchaseReturnItem = new PurchaseReturnItem();
			$purchaseReturnItem->purchase_return_id = $purchaseReturn->id;
			$purchaseReturnItem->product_id = $request->product_id[$i];
			$purchaseReturnItem->description = $request->product_description[$i];
			$purchaseReturnItem->quantity = $request->quantity[$i];
			$purchaseReturnItem->unit_cost = $request->unit_cost[$i];
			$purchaseReturnItem->discount = $request->discount[$i];
			$purchaseReturnItem->tax_method = $request->tax_method[$i];
			$purchaseReturnItem->tax_id = $request->tax_id[$i];
			$purchaseReturnItem->tax_amount = $request->tax_amount[$i];
			$purchaseReturnItem->sub_total = $request->sub_total[$i];
			$purchaseReturnItem->company_id = $company_id;
			$purchaseReturnItem->save();

			//Update Stock
			$stock = Stock::where("product_id", $purchaseReturnItem->product_id)
						  ->where("company_id",$company_id)->first();
			$stock->quantity = $stock->quantity - $purchaseReturnItem->quantity;
			$stock->company_id = $company_id;
			$stock->save();
		}

		//Credit Account
		$transaction = new Transaction();
	    $transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = convert_currency(base_currency(),$transaction->account->account_currency,$purchaseReturn->grand_total);
		$transaction->base_amount = $purchaseReturn->grand_total;
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->purchase_return_id = $purchaseReturn->id;
		$transaction->note = $request->input('note');
		$transaction->company_id = $company_id;
		
        $transaction->save();
		
		DB::commit();

        
		if(! $request->ajax()){
           return redirect('purchase_returns/create')->with('success', _lang('Purchase Returned Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Purchase Returned Sucessfully'),'data'=>$purchase]);
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
        $purchase = PurchaseReturn::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.purchase_return.view',compact('purchase','id'));
		}else{
			return view('backend.accounting.purchase_return.modal.view',compact('purchase','id'));
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
        $purchase = PurchaseReturn::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.purchase_return.edit',compact('purchase','id'));
		}else{
           return view('backend.accounting.purchase_return.modal.edit',compact('purchase','id'));
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
			'account_id' => 'required',
			'sub_total.*' => 'required|numeric',
			'attachemnt' => 'nullable|mimes:jpeg,png,jpg,doc,pdf,docx,zip',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('purchase_returns.edit', $id)
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
		

        $purchaseReturn= PurchaseReturn::where("id",$id)->where("company_id",$company_id)->first();
	    $purchaseReturn->return_date = $request->input('return_date');
		$purchaseReturn->supplier_id = $request->input('supplier_id');
		$purchaseReturn->account_id = $request->input('account_id');
		$purchaseReturn->chart_id = $request->input('chart_id');
		$purchaseReturn->payment_method_id = $request->input('payment_method_id');

		if($request->input('return_tax_id') != ""){
			$purchaseReturn->tax_id = $request->input('return_tax_id');
			$tax = Tax::find($purchaseReturn->tax_id);
			if($tax->type == "percent"){
				$purchaseReturn->tax_amount =  $request->input('product_total') * $tax->rate/(100+$tax->rate);
			}else if($tax->type == "fixed"){
				$purchaseReturn->tax_amount = $tax->rate;
			}		
		}else{
			$purchaseReturn->tax_id = null;
			$purchaseReturn->tax_amount = 0;
		}

		$purchaseReturn->product_total = $request->input('product_total');
		$purchaseReturn->grand_total = ($purchaseReturn->product_total + $purchaseReturn->tax_amount);
		$purchaseReturn->attachemnt = $attachemnt;
		$purchaseReturn->note = $request->input('note');
		$purchaseReturn->company_id = $company_id;
	
		$purchaseReturn->save();


		//Remove Previous Purcahse item
		$previous_items = PurchaseReturnItem::where("purchase_return_id",$id)->get();
		foreach($previous_items as $p_item){
			$returnItem = PurchaseReturnItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}


		for($i=0; $i<count($request->product_id); $i++ ){
			$returnItem = new PurchaseReturnItem();
			$returnItem->purchase_return_id = $purchaseReturn->id;
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

		//Update Credit Account
		$transaction = Transaction::where('purchase_return_id',$purchaseReturn->id)->first();
		$transaction->trans_date = date('Y-m-d');
		$transaction->account_id = $request->input('account_id');
		$transaction->chart_id = $request->input('chart_id');
		$transaction->type = 'income';
		$transaction->dr_cr = 'cr';
		$transaction->amount = convert_currency(base_currency(),$transaction->account->account_currency,$purchaseReturn->grand_total);
		$transaction->base_amount = $purchaseReturn->grand_total;
		$transaction->payment_method_id = $request->input('payment_method_id');
		$transaction->purchase_return_id = $purchaseReturn->id;
		$transaction->note = $request->input('note');
		$transaction->company_id = company_id();
		
		$transaction->save();
		
		DB::commit();
		
		
		if(! $request->ajax()){
           return redirect('purchase_returns')->with('success', _lang('Updated Sucessfully'));
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
		
        $purchaseReturn = PurchaseReturn::where("id",$id)
									    ->where("company_id",company_id());

		$transaction = Transaction::where('purchase_return_id', $purchaseReturn->id)->first();	
		$transaction->delete();				

		$purchaseReturn->delete();
		
		//Remove Purchase Item
		$purchaseReturnItems = PurchaseReturnItem::where("purchase_return_id",$id)->get();
		foreach($purchaseReturnItems as $p_item){
			$returnItem = PurchaseReturnItem::find($p_item->id);
			$returnItem->delete();
			$this->update_stock($p_item->product_id);
		}
		
		DB::commit();

        return redirect('purchase_returns')->with('success',_lang('Deleted Sucessfully'));
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
