<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProjectMilestone;
use Validator;
use Auth;

class ProjectMilestoneController extends Controller
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
        if( $request->ajax()){
           return view('backend.accounting.project_milestone.modal.create');
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
            'title' => 'required',
			'due_date' => 'required',
			'status' => 'required',
			'cost' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('project_milestones.create')
                	             ->withErrors($validator)
                	             ->withInput();
            }			
        }
	
        

        $projectmilestone = new ProjectMilestone();
        $projectmilestone->title = $request->input('title');
		$projectmilestone->description = $request->input('description');
		$projectmilestone->due_date = $request->input('due_date');
		$projectmilestone->status = $request->input('status');
		$projectmilestone->cost = $request->input('cost');
        $projectmilestone->project_id = $request->input('project_id');
		$projectmilestone->user_id = Auth::id();
		$projectmilestone->company_id = company_id();

        $projectmilestone->save();

        create_log('projects', $projectmilestone->project_id, _lang('Create New Project Milestone'));

        //Prefix Output
        $projectmilestone->status = project_status($projectmilestone->status);
        $projectmilestone->cost = decimalPlace($projectmilestone->cost,currency());

        if(! $request->ajax()){
           return back()->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$projectmilestone, 'table' => '#project_milestones_table']);
        }
        
   }

   /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get_milestones($project_id)
    {
        $milestones = ProjectMilestone::where('project_id',$project_id)           
                                      ->where('company_id',company_id())
                                      ->get();
        echo json_encode($milestones);
        
    }
	

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $projectmilestone = ProjectMilestone::where('id',$id)
                                            ->where('company_id',company_id())
                                            ->first();
        if($request->ajax()){
            return view('backend.accounting.project_milestone.modal.view',compact('projectmilestone','id'));
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
        $projectmilestone = ProjectMilestone::where('id',$id)
                                            ->where('company_id',company_id())
                                            ->first();
        if($request->ajax()){
            return view('backend.accounting.project_milestone.modal.edit',compact('projectmilestone','id'));
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
            'title' => 'required',
            'due_date' => 'required',
            'status' => 'required',
            'cost' => 'nullable|numeric',
        ]);


		if ($validator->fails()) {
			if($request->ajax()){ 
				return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('project_milestones.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $projectmilestone = ProjectMilestone::where('id',$id)
                                            ->where('company_id',company_id())
                                            ->first();
		$projectmilestone->title = $request->input('title');
		$projectmilestone->description = $request->input('description');
		$projectmilestone->due_date = $request->input('due_date');
		$projectmilestone->status = $request->input('status');
		$projectmilestone->cost = $request->input('cost');
        $projectmilestone->project_id = $request->input('project_id');
		//$projectmilestone->user_id = Auth::id();
        $projectmilestone->company_id = company_id();
	
        $projectmilestone->save();

        create_log('projects', $projectmilestone->project_id, _lang('Update Project Milestone'));

        //Prefix Output
        $projectmilestone->status = project_status($projectmilestone->status);
        $projectmilestone->cost = decimalPlace($projectmilestone->cost,currency());
		
		if(! $request->ajax()){
           return back()->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$projectmilestone, 'table' => '#project_milestones_table']);
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
        $projectmilestone = ProjectMilestone::where('id',$id)
                                            ->where('company_id',company_id());
        create_log('projects', $projectmilestone->project_id, _lang('Remove Project Milestone'));
        $projectmilestone->delete();

        if(! $request->ajax()){
           return back()->with('success', _lang('Deleted Sucessfully'));
        }else{
           return response()->json(['result'=>'success', 'message'=>_lang('Deleted Sucessfully'), 'id'=>$id, 'table' => '#project_milestones_table']);
        }
    }
}