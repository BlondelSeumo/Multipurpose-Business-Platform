<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tax;
use Validator;
use Illuminate\Validation\Rule;

class TaxController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxs = Tax::where("company_id",company_id())
                    ->orderBy("id","desc")->get();
        return view('backend.accounting.tax.list',compact('taxs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.tax.create');
		}else{
           return view('backend.accounting.tax.modal.create');
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
			'tax_name' => 'required|max:30',
		    'rate' => 'required|numeric',
		    'type' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('taxs/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			 
		
        $tax= new Tax();
	    $tax->tax_name = $request->input('tax_name');
        $tax->rate = $request->input('rate');
        $tax->type = $request->input('type');
	    $tax->company_id = company_id();
	
        $tax->save();
        if($tax->type == "percent"){
            $tax->rate = currency()." ".decimalPlace($tax->rate)."%";
        }else{
            $tax->rate = currency()." ".decimalPlace($tax->rate);
        }
        
		if(! $request->ajax()){
           return redirect('taxs/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$tax]);
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
        $tax = Tax::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.tax.view',compact('tax','id'));
		}else{
			return view('backend.accounting.tax.modal.view',compact('tax','id'));
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
        $tax = Tax::where("id",$id)->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.tax.edit',compact('tax','id'));
		}else{
           return view('backend.accounting.tax.modal.edit',compact('tax','id'));
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
			'tax_name' => 'required|max:30',
            'rate' => 'required|numeric',
            'type' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('taxs.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		
        $tax = Tax::where("id",$id)->where("company_id",company_id())->first();
		$tax->tax_name = $request->input('tax_name');
        $tax->rate = $request->input('rate');
        $tax->type = $request->input('type');
        $tax->company_id = company_id();
	
        $tax->save();
        if($tax->type == "percent"){
            $tax->rate = currency()." ".decimalPlace($tax->rate)."%";
        }else{
            $tax->rate = currency()." ".decimalPlace($tax->rate);
        }
		
		if(! $request->ajax()){
           return redirect('taxs')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$tax]);
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
        $tax = Tax::where("id",$id)
                  ->where("company_id",company_id());
        $tax->delete();
        return redirect('taxs')->with('success',_lang('Information has been deleted sucessfully'));
    }
}
