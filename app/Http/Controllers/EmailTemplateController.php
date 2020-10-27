<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailTemplate;
use Validator;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emailtemplates = EmailTemplate::all()->sortByDesc("id");
        return view('backend.administration.email_template.list',compact('emailtemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /*public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.administration.email_template.create');
		}else{
           return view('backend.administration.email_template.modal.create');
		}
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function store(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'name' => '',
			'subject' => 'required',
			'body' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('email_templates/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $emailtemplate= new EmailTemplate();
	    $emailtemplate->name = $request->input('name');
		$emailtemplate->subject = $request->input('subject');
		$emailtemplate->body = $request->input('body');
	
        $emailtemplate->save();
        
		if(! $request->ajax()){
           return redirect('email_templates/create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$emailtemplate]);
		}
        
   }*/
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $emailtemplate = EmailTemplate::find($id);
		if(! $request->ajax()){
		    return view('backend.administration.email_template.view',compact('emailtemplate','id'));
		}else{
			return view('backend.administration.email_template.modal.view',compact('emailtemplate','id'));
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
        $emailtemplate = EmailTemplate::find($id);
		if(! $request->ajax()){
		   return view('backend.administration.email_template.edit',compact('emailtemplate','id'));
		}else{
           return view('backend.administration.email_template.modal.edit',compact('emailtemplate','id'));
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
			'name' => '',
			'subject' => 'required',
			'body' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('email_templates.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $emailtemplate = EmailTemplate::find($id);
		$emailtemplate->subject = $request->input('subject');
		$emailtemplate->body = $request->input('body');
	
        $emailtemplate->save();
		
		if(! $request->ajax()){
           return redirect('email_templates')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$emailtemplate]);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
        $emailtemplate = EmailTemplate::find($id);
        $emailtemplate->delete();
        return redirect('email_templates')->with('success',_lang('Removed Sucessfully'));
    }*/
}
