<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Item;
use App\Stock;
use Validator;
use Illuminate\Validation\Rule;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ProductController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Item::where("company_id",company_id())
                     ->where("item_type","product")
                     ->orderBy("id","desc")->get();
        return view('backend.accounting.product.list',compact('items'));
    }

    /** Excel Import**/
    public function import(Request $request)
    {       
        if($request->isMethod('get')){
            return view('backend.accounting.product.import');
        }else{
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $validator = Validator::make($request->all(), [
                'file' => 'required|mimes:xlsx',
            ]);
            
            if ($validator->fails()) {
                if($request->ajax()){ 
                    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
                }else{
                    return redirect('products/import')->withErrors($validator)
                                                      ->withInput();
                }           
            }
                
            $new_rows = 0;

            DB::beginTransaction();
            
            $previous_rows = Item::where('company_id',company_id())->count();

            $import = Excel::import(new ProductsImport, request()->file('file'));

            $current_rows = Item::where('company_id',company_id())->count();

            $new_rows = $current_rows - $previous_rows;

            DB::commit();

            return back()->with('success',$new_rows.' '._lang('Rows Imported Sucessfully'));
        }           
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.product.create');
		}else{
           return view('backend.accounting.product.modal.create');
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
			'item_name' => 'required',
            'product_cost' => 'required|numeric',
            'product_price' => 'required|numeric',
            'product_unit' => 'required',
            'tax_method' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('products/create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		DB::beginTransaction();	
		
        //Create Item
        $item = new Item();
        $item->item_name = $request->input('item_name');
        $item->item_type = 'product';
        $item->company_id = company_id();
        $item->save();


	    //Create Product
        $product = new Product();
	    $product->item_id = $item->id;
        $product->supplier_id = $request->input('supplier_id');
        $product->product_cost = $request->input('product_cost');
        $product->product_price = $request->input('product_price');
        $product->product_unit = $request->input('product_unit');
        $product->tax_method = $request->input('tax_method');
        $product->tax_id = $request->input('tax_id');
        $product->description = $request->input('description');
        
        $product->save();

        //Create Stock Row
        $stock = new Stock();
        $stock->product_id = $item->id;
        $stock->quantity = 0;
        $stock->company_id = company_id();
        $stock->save();
		
		DB::commit();
        
		if(! $request->ajax()){
           return redirect('products/create')->with('success', _lang('Saved sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved sucessfully'),'data'=>$product]);
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
        $item = Item::where("id",$id)->where("company_id",company_id())->first();

		if(! $request->ajax()){
		    return view('backend.accounting.product.view',compact('item','id'));
		}else{
			return view('backend.accounting.product.modal.view',compact('item','id'));
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
        $item = Item::where("id",$id)->where("company_id",company_id())->first();

		if(! $request->ajax()){
		   return view('backend.accounting.product.edit',compact('item','id'));
		}else{
           return view('backend.accounting.product.modal.edit',compact('item','id'));
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
			'item_name' => 'required',
            'product_cost' => 'required|numeric',
            'product_price' => 'required|numeric',
            'product_unit' => 'required',
            'tax_method' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('products.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
    
	  
        //Update item
		DB::beginTransaction();
        $item = Item::where("id",$id)->where("company_id",company_id())->first();
        
        if( $item ){
				
            $item->item_name = $request->input('item_name');
            $item->item_type = 'product';
            $item->company_id = company_id();
            $item->save();

            
            $product = Product::where("item_id",$id)->first();
            $product->item_id = $item->id;
            $product->supplier_id = $request->input('supplier_id');
            $product->product_cost = $request->input('product_cost');
            $product->product_price = $request->input('product_price');
            $product->product_unit = $request->input('product_unit');
            $product->tax_method = $request->input('tax_method');
            $product->tax_id = $request->input('tax_id');
            $product->description = $request->input('description');
        
            $product->save();
			
			DB::commit();
        }else{
            if(! $request->ajax()){
                return redirect('products')->with('error', _lang('Update Failed !'));
            }else{
                return response()->json(['result'=>'error','message'=>_lang('Update Failed !')]);
            }
        }

		
		if(! $request->ajax()){
           return redirect('products')->with('success', _lang('Updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated sucessfully'),'data'=>$product]);
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
        $item = Item::where("id",$id)->where("company_id",company_id());
        $item->delete();

        $product = Product::where("item_id",$id);
        $product->delete();
		DB::commit();
        return redirect('products')->with('success',_lang('Deleted sucessfully'));
    }


    public function get_product(Request $request,$id)
    {
        $item = Item::where("id",$id)->where("company_id",company_id())->first();
        
		if($item->item_type == 'product'){
			echo json_encode(array("item"=>$item,"product"=>$item->product,"tax"=>$item->product->tax,"available_quantity"=>$item->product_stock->quantity));
        }else if($item->item_type == 'service'){
			echo json_encode(array("item"=>$item,"product"=>$item->service,"tax"=>$item->service->tax));
		}
	}

}
