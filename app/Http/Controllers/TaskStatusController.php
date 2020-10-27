<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TaskStatus;
use Validator;
use Illuminate\Validation\Rule;

class TaskStatusController extends Controller
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
		   return view('backend.accounting.general_settings.task_status.create');
		}else{
           return view('backend.accounting.general_settings.task_status.modal.create');
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
                Rule::unique('task_statuses')->where('company_id',company_id()),
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
			
		
        $taskstatus = new TaskStatus();
	    $taskstatus->title = $request->input('title');
		$taskstatus->color = $request->input('color');
		$taskstatus->order = $request->input('order');
		$taskstatus->company_id = company_id();
	
        $taskstatus->save();
		
		//Prefix Output
		$taskstatus->color = '<div class="rounded-circle color-circle" style="background:'. $taskstatus->color .'"></div>';
        
		if(! $request->ajax()){
           return redirect()->route('task_statuses.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$taskstatus, 'table' => '#task_status_table']);
		}
        
   }

    /** Update Task Status Order **/
    public function update_task_status_order(Request $request, $task_status_id, $order){

        if( $request->ajax()){
            $task_status = TaskStatus::where('id', $task_status_id)
									 ->where('company_id', company_id())
								     ->first();
             
            if($task_status){
               $task_status2 = TaskStatus::where('order', $order)
										 ->where('company_id', company_id())
										 ->first();
	           if($task_status2){
					$task_status2->order = $task_status->order;
              		$task_status2->save();
	           }

               $task_status->order = $order;
               $task_status->save();
               echo json_encode($task_status); 
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
        $taskstatus = TaskStatus::where('id',$id)
		                        ->where('company_id', company_id())
		                        ->first();
		if(! $request->ajax()){
		   return view('backend.accounting.general_settings.task_status.edit',compact('taskstatus','id'));
		}else{
           return view('backend.accounting.general_settings.task_status.modal.edit',compact('taskstatus','id'));
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
	
        	
		
        $taskstatus = TaskStatus::where('id',$id)
		                        ->where('company_id', company_id())
		                        ->first();

	    $task_status2 = TaskStatus::where('order',$request->order)
                                  ->where('company_id', company_id())
                                  ->first();
        if($task_status2){
			$task_status2->order = $taskstatus->order;
      		$task_status2->save();
        }                       
		$taskstatus->title = $request->input('title');
		$taskstatus->color = $request->input('color');
		$taskstatus->order = $request->input('order');
		$taskstatus->company_id = company_id();


	
        $taskstatus->save();
		
		//Prefix Output
		$taskstatus->color = '<div class="rounded-circle color-circle" style="background:'. $taskstatus->color .'"></div>';
		
		if(! $request->ajax()){
           return redirect()->route('task_statuses.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$taskstatus, 'table' => '#task_status_table']);
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
        $taskstatus = TaskStatus::where('id',$id)
		                        ->where('company_id', company_id());
        $taskstatus->delete();

		if(! $request->ajax()){
           return back()->with('success', _lang('Deleted Sucessfully'));
        }else{
           return response()->json(['result'=>'success', 'message'=>_lang('Deleted Sucessfully'), 'id'=>$id, 'table' => '#task_status_table']);
        }
    }
}
