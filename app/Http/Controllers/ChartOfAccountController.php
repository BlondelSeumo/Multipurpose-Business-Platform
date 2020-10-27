<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ChartOfAccount;
use Validator;
use Illuminate\Validation\Rule;

class ChartOfAccountController extends Controller
{
	
	public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $chartofaccounts = ChartOfAccount::where("company_id",company_id())
										  ->orderBy("id","desc")->get();
        return view('backend.accounting.chart_of_account.list',compact('chartofaccounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.chart_of_account.create');
		}else{
           return view('backend.accounting.chart_of_account.modal.create');
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
			'name' => 'required|max:50',
			'type' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('chart_of_accounts/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			

        $chartofaccount= new ChartOfAccount();
	    $chartofaccount->name = $request->input('name');
		$chartofaccount->type = $request->input('type');
		$chartofaccount->company_id = company_id();
	
        $chartofaccount->save();
        
		if(! $request->ajax()){
           return redirect('chart_of_accounts/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$chartofaccount]);
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
        $chartofaccount = ChartOfAccount::where("id",$id)
										->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.chart_of_account.view',compact('chartofaccount','id'));
		}else{
			return view('backend.accounting.chart_of_account.modal.view',compact('chartofaccount','id'));
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
        $chartofaccount = ChartOfAccount::where("id",$id)
										->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.chart_of_account.edit',compact('chartofaccount','id'));
		}else{
           return view('backend.accounting.chart_of_account.modal.edit',compact('chartofaccount','id'));
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
			'name' => 'required|max:50',
			'type' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('chart_of_accounts.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	

		$chartofaccount = ChartOfAccount::where("id",$id)->where("company_id",company_id())->first();
		$chartofaccount->name = $request->input('name');
		$chartofaccount->type = $request->input('type');
		$chartofaccount->company_id = company_id();
	
        $chartofaccount->save();
		
		if(! $request->ajax()){
           return redirect('chart_of_accounts')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$chartofaccount]);
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
        $chartofaccount = ChartOfAccount::where("id",$id)->where("company_id",company_id());
        $chartofaccount->delete();
        return redirect('chart_of_accounts')->with('success',_lang('Removed Sucessfully'));
    }
}
