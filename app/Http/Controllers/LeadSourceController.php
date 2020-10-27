<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeadSource;
use Validator;

class LeadSourceController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka'))); 

        $this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'project_management_module' ) ){
                    if( ! $request->ajax()){
                        return redirect('membership/extend')->with('message', _lang('Sorry, This feature is not available in your current subscription. You can upgrade your package !'));
                    }else{
                        return response()->json(['result'=>'error','message'=>_lang('Sorry, This feature is not available in your current subscription !')]);
                    }
                }
            }

            return $next($request);
        });
    }
	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if( ! $request->ajax()){
           return view('backend.accounting.general_settings.lead_source.create');
        }else{
           return view('backend.accounting.general_settings.lead_source.modal.create');
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
            'title' => 'required|max:50',
			'order' => '',
			'company_id' => '',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('lead_sources.create')
                	             ->withErrors($validator)
                	             ->withInput();
            }			
        }
	
        

        $leadsource = new LeadSource();
        $leadsource->title = $request->input('title');
		//$leadsource->order = $request->input('order');
		$leadsource->company_id = company_id();

        $leadsource->save();

        if(! $request->ajax()){
           return back()->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$leadsource, 'table' => '#lead_source_table']);
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
        $leadsource = LeadSource::where('id',$id)
                                ->where('company_id', company_id())
                                ->first();
        if(! $request->ajax()){
            return view('backend.accounting.general_settings.lead_source.edit',compact('leadsource','id'));
        }else{
            return view('backend.accounting.general_settings.lead_source.modal.edit',compact('leadsource','id'));
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
			'title' => 'required|max:50',
			'order' => '',
			'company_id' => '',
		]);

		if ($validator->fails()) {
			if($request->ajax()){ 
				return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('lead_sources.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $leadsource = LeadSource::where('id',$id)
                                ->where('company_id', company_id())
                                ->first();
		$leadsource->title = $request->input('title');
		//$leadsource->order = $request->input('order');
		$leadsource->company_id = company_id();
	
        $leadsource->save();
		
		if(! $request->ajax()){
           return back()->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$leadsource, 'table' => '#lead_source_table']);
		}
	    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $leadsource = LeadSource::where('id',$id)
                                ->where('company_id', company_id());
        $leadsource->delete();
		
        if(! $request->ajax()){
           return back()->with('success', _lang('Deleted Sucessfully'));
        }else{
           return response()->json(['result'=>'success', 'message'=>_lang('Deleted Sucessfully'), 'id'=>$id, 'table' => '#lead_source_table']);
        }
    }
}