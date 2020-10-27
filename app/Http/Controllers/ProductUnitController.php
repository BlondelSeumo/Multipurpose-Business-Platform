<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductUnit;
use Validator;
use Illuminate\Validation\Rule;

class ProductUnitController extends Controller
{
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productunits = ProductUnit::where("company_id",company_id())
                                   ->orderBy("id","desc")->get();
        return view('backend.accounting.general_settings.product_unit.list',compact('productunits'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.general_settings.product_unit.create');
		}else{
           return view('backend.accounting.general_settings.product_unit.modal.create');
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
			'unit_name' => 'required|max:191',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('product_units/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $productunit= new ProductUnit();
	    $productunit->unit_name = $request->input('unit_name');
	    $productunit->company_id = company_id();
	
        $productunit->save();
        
		if(! $request->ajax()){
           return redirect('product_units/create')->with('success', _lang('Saved sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved sucessfully'),'data'=>$productunit]);
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
        $productunit = ProductUnit::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.general_settings.product_unit.edit',compact('productunit','id'));
		}else{
           return view('backend.accounting.general_settings.product_unit.modal.edit',compact('productunit','id'));
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
			'unit_name' => 'required|max:191',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('product_units.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $productunit = ProductUnit::where("id",$id)->where("company_id",company_id())->first();
		$productunit->unit_name = $request->input('unit_name');
	    $productunit->company_id = company_id();
	
        $productunit->save();
		
		if(! $request->ajax()){
           return redirect('product_units')->with('success', _lang('Updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated sucessfully'),'data'=>$productunit]);
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
        $productunit = ProductUnit::where("id",$id)->where("company_id",company_id());
        $productunit->delete();
        return redirect('product_units')->with('success',_lang('Deleted sucessfully'));
    }
}
