<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TimeSheet;
use Validator;
use Auth;

class TimeSheetController extends Controller
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
           return view('backend.accounting.timesheet.create');
        }else{
           return view('backend.accounting.timesheet.modal.create');
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
            'start_time' => 'required|date',
			'end_time' => 'required|date|after:start_time',
			'user_id' => 'required',
			'task_id' => 'required',
			'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('timesheets.create')
                	             ->withErrors($validator)
                	             ->withInput();
            }			
        }
	    

        $timesheet = new TimeSheet();
        $timesheet->start_time = $request->input('start_time');
		$timesheet->end_time = $request->input('end_time');
        //$timesheet->total_hour = (strtotime($timesheet->end_time) - strtotime($timesheet->start_time))/3600;
        $timesheet->total_hour = time_from_seconds( (strtotime($timesheet->end_time) - strtotime($timesheet->start_time)) );
		$timesheet->user_id = Auth::id();
        $timesheet->project_id = $request->input('project_id');
		$timesheet->task_id = $request->input('task_id');
		$timesheet->note = $request->input('note');
		$timesheet->company_id = company_id();

        $timesheet->save();

        create_log('projects', $timesheet->project_id, _lang('Log new timesheet'));

        //Prefix Output
        $date_format = get_company_option('date_format','Y-m-d');
        $time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A';
        $timesheet->start_time = date("$date_format $time_format",strtotime($timesheet->start_time));
        $timesheet->end_time = date("$date_format $time_format",strtotime($timesheet->end_time));
        $timesheet->user_id = $timesheet->user->name;
        $timesheet->task_id = $timesheet->task->title;

        if(! $request->ajax()){
           return redirect()->route('timesheets.create')->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$timesheet, 'table' => '#timesheets_table']);
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
        $timesheet = TimeSheet::where('id',$id)
                              ->where('company_id',company_id())
                              ->first();
        if(! $request->ajax()){
            return view('backend.accounting.timesheet.view',compact('timesheet','id'));
        }else{
            return view('backend.accounting.timesheet.modal.view',compact('timesheet','id'));
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
        $timesheet = TimeSheet::where('id',$id)
                              ->where('company_id',company_id())
                              ->first();
        if(! $request->ajax()){
            return view('backend.accounting.timesheet.edit',compact('timesheet','id'));
        }else{
            return view('backend.accounting.timesheet.modal.edit',compact('timesheet','id'));
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
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'user_id' => 'required',
            'task_id' => 'required',
            'project_id' => 'required',
        ]);

		if ($validator->fails()) {
			if($request->ajax()){ 
				return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('timesheets.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		
        $timesheet = TimeSheet::where('id',$id)
                              ->where('company_id',company_id())
                              ->first();
		$timesheet->start_time = $request->input('start_time');
        $timesheet->end_time = $request->input('end_time');
        $timesheet->total_hour = time_from_seconds( (strtotime($timesheet->end_time) - strtotime($timesheet->start_time)) );
        $timesheet->user_id = Auth::id();
        //$timesheet->project_id = $request->input('project_id');
        $timesheet->task_id = $request->input('task_id');
        $timesheet->note = $request->input('note');
        $timesheet->company_id = company_id();
	
        $timesheet->save();

        create_log('projects', $timesheet->project_id, _lang('Update timesheet').' - #'.$timesheet->id);

        //Prefix Output
        $date_format = get_company_option('date_format','Y-m-d');
        $time_format = get_company_option('time_format',24) == '24' ? 'H:i' : 'h:i A';
        $timesheet->start_time = date("$date_format $time_format",strtotime($timesheet->start_time));
        $timesheet->end_time = date("$date_format $time_format",strtotime($timesheet->end_time));
        $timesheet->user_id = $timesheet->user->name;
        $timesheet->task_id = $timesheet->task->title;
		
		if(! $request->ajax()){
           return redirect()->route('timesheets.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$timesheet, 'table' => '#timesheets_table']);
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
        $timesheet = TimeSheet::where('id',$id)
                              ->where('company_id',company_id())
                              ->first();
        create_log('projects', $timesheet->project_id, _lang('Remove timesheet').' - #'.$timesheet->id);                     
        $timesheet->delete();
        return redirect()->route('timesheets.index')->with('success',_lang('Deleted Sucessfully'));
    }
}