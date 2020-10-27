<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyEmailTemplate;
use Validator;
use Illuminate\Validation\Rule;

class CompanyEmailTemplateController extends Controller
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
        $companyemailtemplates = CompanyEmailTemplate::where("company_id",company_id())
													 ->orderBy("id","desc")->get();
        return view('backend.accounting.company_email_template.list',compact('companyemailtemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.company_email_template.create');
		}else{
           return view('backend.accounting.company_email_template.modal.create');
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
			'related_to' => 'required',
			'name' => 'required|max:191',
			'subject' => 'required|max:191',
			'body' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('company_email_template/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $companyemailtemplate = new CompanyEmailTemplate();
	    $companyemailtemplate->related_to = $request->input('related_to');
	    $companyemailtemplate->name = $request->input('name');
		$companyemailtemplate->subject = $request->input('subject');
		$companyemailtemplate->body = $request->input('body');
		$companyemailtemplate->company_id = company_id();
	
        $companyemailtemplate->save();
        
		if(! $request->ajax()){
           return redirect('company_email_template/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$companyemailtemplate]);
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
        $companyemailtemplate = CompanyEmailTemplate::where("id",$id)
													->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.company_email_template.view',compact('companyemailtemplate','id'));
		}else{
			return view('backend.accounting.company_email_template.modal.view',compact('companyemailtemplate','id'));
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
        $companyemailtemplate = CompanyEmailTemplate::where("id",$id)
													->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.accounting.company_email_template.edit',compact('companyemailtemplate','id'));
		}else{
           return view('backend.accounting.company_email_template.modal.edit',compact('companyemailtemplate','id'));
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
			'related_to' => 'required',
			'name' => 'required|max:191',
			'subject' => 'required|max:191',
			'body' => 'required',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('company_email_template.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $companyemailtemplate = CompanyEmailTemplate::where("id",$id)->where("company_id",company_id())->first();
		$companyemailtemplate->related_to = $request->input('related_to');
		$companyemailtemplate->name = $request->input('name');
		$companyemailtemplate->subject = $request->input('subject');
		$companyemailtemplate->body = $request->input('body');
		$companyemailtemplate->company_id = company_id();
	
        $companyemailtemplate->save();
		
		if(! $request->ajax()){
           return redirect('company_email_template')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$companyemailtemplate]);
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
        $companyemailtemplate = CompanyEmailTemplate::where("id",$id)
													->where("company_id",company_id());
        $companyemailtemplate->delete();
        return redirect('company_email_template')->with('success',_lang('Deleted Sucessfully'));
    }
	
	public function get_template($id){
		$companyemailtemplate = CompanyEmailTemplate::where("id",$id)
													->where("company_id",company_id())->first();
	    echo json_encode($companyemailtemplate);
	}
}
