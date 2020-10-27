<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\TaskStatus;
use Validator;
use DataTables;
use Auth;
use DB;
use Notification;
use App\Notifications\TaskCreated;
use App\Notifications\TaskUpdated;

class TaskController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($view_type = '')
    {
        if($view_type == 'kanban'){
            $company_id = company_id();

            $task_status = TaskStatus::where('company_id', $company_id)
                                     ->orderBy('order','asc')
                                     ->get();                                   
            return view('backend.accounting.task.kanban_view',compact('task_status'));
        }else{
            return view('backend.accounting.task.list');
        }
        
    }

    /* Get Logs Data*/
    public function load_more_task(Request $request, $status_id, $task_id){

        if( $request->ajax()){
            $tasks = Task::where('company_id', company_id())
                         ->where('task_status_id',$status_id)
                         ->where('id','<', $task_id)
                         ->with('assigned_user')
                         ->latest()
                         ->limit(20)
                         ->get();
            echo json_encode($tasks);  
        }                                      
    }

    /** Update Lead Status **/
    public function update_task_status(Request $request, $status_id, $task_id){

        if( $request->ajax()){
            $task = Task::where('id',$task_id)
                        ->where('company_id', company_id())
                        ->first();
             
            if($task){
               $task->task_status_id = $status_id;
               $task->save();
               echo json_encode($task); 
            } 
        }                                      
    }
	
	public function get_table_data(Request $request){
		$user_type = Auth::user()->user_type;

		$tasks = Task::select('tasks.*')
                      ->with('project')
                      ->with('assigned_user')
                      ->with('status')
                      ->where('company_id',company_id())
                      ->when($user_type, function ($query, $user_type) {
                           if($user_type == 'staff'){
                                return $query->where('assigned_user_id', Auth::id());
                           }
                      })
					  ->orderBy("tasks.id","desc");

		return Datatables::eloquent($tasks)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('project_id')) {
                                $query->where('project_id', 'like', "%{$request->get('project_id')}%");
                            }

                            if ($request->has('assigned_user_id')) {
                                $query->where('assigned_user_id', 'like', "%{$request->get('assigned_user_id')}%");
                            }

                            if ($request->has('task_status_id')) {
                                $query->whereIn('task_status_id', json_decode($request->get('task_status_id')));
                            }

                            if ($request->has('date_range')) {
                                $date_range = explode(" - ",$request->get('date_range'));
                                $query->whereBetween('end_date', [$date_range[0], $date_range[1]]);
                            }
                        })
                        ->editColumn('title', function ($task) {
                            return '<a href="'.action('TaskController@show', $task->id).'" class="ajax-modal" data-title="'.$task->title.'">'. $task->title .'</a>';
                        })
                        ->editColumn('project.name', function ($task) {
                            return '<a href="'.action('ProjectController@show', $task->project_id).'">'. $task->project->name .'</a>';
                        })
                        ->editColumn('priority', function ($task) {
                            return task_priority($task->priority);
                        })
                        ->editColumn('status.title', function ($task) {
                            $status_color = $task->status->color;
                            return "<span class='badge badge-primary' style='background:{$status_color}'>{$task->status->title}</span>";
                        })
                        ->editColumn('assigned_user.name', function ($task) {
                            if($task->assigned_user_id != ''){
                                return '<img src="'. asset('public/uploads/profile/'.$task->assigned_user->profile_picture) .'" class="project-avatar" data-toggle="tooltip" data-placement="top" title="'. $task->assigned_user->name .'">&nbsp'.$task->assigned_user->name;
                            }
                        })
                        ->editColumn('start_date', function ($task) {
                            $date_format = get_company_option('date_format','Y-m-d');
                            return date("$date_format",strtotime($task->start_date));
                        })
                        ->editColumn('end_date', function ($task) {
                            $date_format = get_company_option('date_format','Y-m-d');
                            return date("$date_format",strtotime($task->end_date));
                        })
						->addColumn('action', function ($task) {
								return '<form action="'.action('TaskController@destroy', $task['id']).'" class="text-center" method="post">'
								.'<a href="'.action('TaskController@show', $task['id']).'" data-title="'. $task->title .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>&nbsp;'
								.'<a href="'.action('TaskController@edit', $task['id']).'" data-title="'. _lang('Update Task') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>&nbsp;'
								.csrf_field()
								.'<input name="_method" type="hidden" value="DELETE">'
								.'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
								.'</form>';
						})
						->setRowId(function ($task) {
							return "row_".$task->id;
						})
						->rawColumns(['title', 'project.name', 'priority',  'status.title', 'assigned_user.name', 'action'])
						->make(true);							    
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if( ! $request->ajax()){
           return view('backend.accounting.task.create');
        }else{
           return view('backend.accounting.task.modal.create');
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
			'project_id' => 'required',
			'priority' => 'required',
			'task_status_id' => 'required',
			'assigned_user_id' => '',
			'start_date' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('tasks.create')
                	             ->withErrors($validator)
                	             ->withInput();
            }			
        }
	 
        DB::beginTransaction();

        $task = new Task();
        $task->title = $request->input('title');
		$task->description = $request->input('description');
		$task->project_id = $request->input('project_id');
		$task->milestone_id = $request->input('milestone_id');
		$task->priority = $request->input('priority');
		$task->task_status_id = $request->input('task_status_id');
		$task->assigned_user_id = $request->input('assigned_user_id');
		$task->start_date = $request->input('start_date');
		$task->end_date = $request->input('end_date');
		$task->user_id = Auth::id();
		$task->company_id = company_id();

        $task->save();

        create_log('projects', $task->project_id, _lang('Create New task'));

        if($task->assigned_user_id != null){
           Notification::send($task->assigned_user, new TaskCreated($task));
        }


        DB::commit();

        if(! $request->ajax()){
           return redirect()->route('tasks.create')->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'), 'data'=>$task, 'table' => '#tasks_table']);
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
        $task = Task::where('id', $id)
                    ->where('company_id',company_id())
                    ->first();
		
        if(! $task){
			if(! $request->ajax()){
				return back()->with('error', _lang('Sorry, Task not found !'));
			}else{
				return response()->json(['result'=>'error', 'message' =>  _lang('Sorry, Task not found !')]);
			}
		}		
					
        if(! $request->ajax()){
            return view('backend.accounting.task.view',compact('task','id'));
        }else{
            return view('backend.accounting.task.modal.view',compact('task','id'));
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
        $task = Task::where('id', $id)
                    ->where('company_id',company_id())
                    ->first();
        if(! $request->ajax()){
            return view('backend.accounting.task.edit',compact('task','id'));
        }else{
            return view('backend.accounting.task.modal.edit',compact('task','id'));
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
			'project_id' => 'required',
			'priority' => 'required',
			'task_status_id' => 'required',
			'assigned_user_id' => '',
			'start_date' => 'required',
		]);

		if ($validator->fails()) {
			if($request->ajax()){ 
				return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('tasks.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $task = Task::where('id', $id)
                    ->where('company_id',company_id())
                    ->first();
		$task->title = $request->input('title');
		$task->description = $request->input('description');
		$task->project_id = $request->input('project_id');
		$task->milestone_id = $request->input('milestone_id');
		$task->priority = $request->input('priority');
		$task->task_status_id = $request->input('task_status_id');
		$task->assigned_user_id = $request->input('assigned_user_id');
		$task->start_date = $request->input('start_date');
		$task->end_date = $request->input('end_date');
		$task->company_id = company_id();
	
        $task->save();

        create_log('projects', $task->project_id, _lang('Update task').' - '.$task->id.'# '.$task->title);

        if($task->assigned_user_id != null){
           Notification::send($task->assigned_user, new TaskUpdated($task));
        }
		
		if(! $request->ajax()){
           return redirect()->route('tasks.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'), 'data'=>$task, 'table' => '#tasks_table']);
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
        $task = Task::where('id', $id)
                    ->where('company_id',company_id())
                    ->first();
        create_log('projects', $task->project_id, _lang('Remove task').' - '.$task->id.'# '.$task->title);           
        $task->delete();
        return redirect()->route('tasks.index')->with('success',_lang('Deleted Sucessfully'));
    }
}