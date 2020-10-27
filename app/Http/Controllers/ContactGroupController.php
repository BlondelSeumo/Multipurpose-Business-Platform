<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ContactGroup;
use Validator;
use Auth;
use Illuminate\Validation\Rule;

class ContactGroupController extends Controller
{
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contactgroups = ContactGroup::where("company_id",company_id())
		                              ->orderBy("id","desc")->get();
        return view('backend.accounting.contacts.contact_group.list',compact('contactgroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.accounting.contacts.contact_group.create');
		}else{
           return view('backend.accounting.contacts.contact_group.modal.create');
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
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect('contact_groups/create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $contactgroup= new ContactGroup();
	    $contactgroup->name = $request->input('name');
		$contactgroup->note = $request->input('note');
		$contactgroup->company_id = company_id();
	
        $contactgroup->save();
        
		if(! $request->ajax()){
           return redirect('contact_groups/create')->with('success', _lang('Saved sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved sucessfully'),'data'=>$contactgroup]);
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
        $contactgroup = ContactGroup::where("id",$id)
                                    ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		    return view('backend.accounting.contacts.contact_group.view',compact('contactgroup','id'));
		}else{
			return view('backend.accounting.contacts.contact_group.modal.view',compact('contactgroup','id'));
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
        $contactgroup = ContactGroup::where("id",$id)
                                    ->where("company_id",company_id())->first();
   
		if(! $request->ajax()){
		   return view('backend.accounting.contacts.contact_group.edit',compact('contactgroup','id'));
		}else{
           return view('backend.accounting.contacts.contact_group.modal.edit',compact('contactgroup','id'));
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
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('contact_groups.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $contactgroup = ContactGroup::where("id",$id)->where("company_id",company_id())->first();
		$contactgroup->name = $request->input('name');
		$contactgroup->note = $request->input('note');
		$contactgroup->company_id = company_id();
	
        $contactgroup->save();
		
		if(! $request->ajax()){
           return redirect('contact_groups')->with('success', _lang('Updated sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated sucessfully'),'data'=>$contactgroup]);
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
        $contactgroup = ContactGroup::where("id",$id)
		                            ->where("company_id",company_id());
        $contactgroup->delete();
        return redirect('contact_groups')->with('success',_lang('Deleted sucessfully'));
    }
}
