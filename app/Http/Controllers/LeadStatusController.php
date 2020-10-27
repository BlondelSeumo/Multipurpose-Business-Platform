<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LeadStatus;
use Validator;
use Illuminate\Validation\Rule;

class LeadStatusController extends Controller
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
		   return view('backend.accounting.general_settings.lead_status.create');
		}else{
           return view('backend.accounting.general_settings.lead_status.modal.create');
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
			'title' => 'required|max:30',
			'color' => 'required|max:10',
			'order' => [
                'required',
                Rule::unique('lead_statuses')->where('company_id',company_id()),
            ],
		],[
			'order.unique' => _lang('You need to add unique order')
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}			
		}
			
		
        $leadstatus = new LeadStatus();
	    $leadstatus->title = $request->input('title');
		$leadstatus->color = $request->input('color');
		$leadstatus->order = $request->input('order');
		$leadstatus->company_id = company_id();
	
        $leadstatus->save();
		
		//Prefix Output
		$leadstatus->color = '<div class="rounded-circle color-circle" style="background:'. $leadstatus->color .'"></div>';
        
		if(! $request->ajax()){
           return redirect()->route('lead_statuses.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$leadstatus, 'table' => '#lead_status_table']);
		}
        
   }

    /** Update Lead Status Order **/
    public function update_lead_status_order(Request $request, $lead_status_id, $order){

        if( $request->ajax()){
            $lead_status = LeadStatus::where('id',$lead_status_id)
	                          ->where('company_id', company_id())
	                          ->first();
             
            if($lead_status){
               $lead_status2 = LeadStatus::where('order',$order)
	                          ->where('company_id', company_id())
	                          ->first();
	           if($lead_status2){
					$lead_status2->order = $lead_status->order;
              		$lead_status2->save();
	           }

               $lead_status->order = $order;
               $lead_status->save();
               echo json_encode($lead_status); 
            } 
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
        $leadstatus = LeadStatus::where('id',$id)
		                        ->where('company_id', company_id())
		                        ->first();
		if(! $request->ajax()){
		   return view('backend.accounting.general_settings.lead_status.edit',compact('leadstatus','id'));
		}else{
           return view('backend.accounting.general_settings.lead_status.modal.edit',compact('leadstatus','id'));
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
			'title' => 'required|max:30',
			'color' => 'required|max:10',
			'order' => 'required',
        ]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}			
		}
	
        	
		
        $leadstatus = LeadStatus::where('id',$id)
		                        ->where('company_id', company_id())
		                        ->first();

	    $lead_status2 = LeadStatus::where('order',$request->order)
                                 ->where('company_id', company_id())
                                 ->first();
        if($lead_status2){
			$lead_status2->order = $leadstatus->order;
      		$lead_status2->save();
        }                       
		$leadstatus->title = $request->input('title');
		$leadstatus->color = $request->input('color');
		$leadstatus->order = $request->input('order');
		$leadstatus->company_id = company_id();


	
        $leadstatus->save();
		
		//Prefix Output
		$leadstatus->color = '<div class="rounded-circle color-circle" style="background:'. $leadstatus->color .'"></div>';
		
		if(! $request->ajax()){
           return redirect()->route('lead_statuses.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$leadstatus, 'table' => '#lead_status_table']);
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
        $leadstatus = LeadStatus::where('id',$id)
		                        ->where('company_id', company_id());
        $leadstatus->delete();
		
        if(! $request->ajax()){
           return back()->with('success', _lang('Deleted Sucessfully'));
        }else{
           return response()->json(['result'=>'success', 'message'=>_lang('Deleted Sucessfully'), 'id'=>$id, 'table' => '#lead_status_table']);
        }
    }
}
