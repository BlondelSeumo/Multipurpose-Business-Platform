<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectMember;
use Validator;
use DataTables;
use Auth;
use DB;
use Notification;
use App\Notifications\ProjectCreated;
use App\Notifications\ProjectUpdated;

class ProjectController extends Controller
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
  public function index()
  {
    return view('backend.accounting.project.list');
  }
  
  public function get_table_data(Request $request){
    $company_id = company_id();
    $user_type = Auth::user()->user_type;

    $projects = Project::select('projects.*')
                           ->with('members')
                           ->with('client')
                           ->where('company_id',$company_id)
                           ->when($user_type, function ($query, $user_type) {
                                if($user_type == 'staff'){
                                   return $query->join('project_members','projects.id','project_members.project_id')
                                                ->where('project_members.user_id',Auth::id());
                                }
                            })
                            ->orderBy("projects.id","desc");


    return Datatables::eloquent($projects)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('client_id')) {
                                $query->where('client_id', 'like', "%{$request->post('client_id')}%");
                            }

                            if ($request->has('status')) {
                                $query->whereIn('status', json_decode($request->post('status')));
                            }

                        })
                        ->editColumn('name', function ($project) {
                            return '<a href="'.action('ProjectController@show', $project['id']).'">'. $project->name .'</a>';
                        })
                        ->editColumn('client.contact_name', function ($project) {
                            return $project->client->contact_name;
                        })
                        ->editColumn('start_date', function ($project) {
                            $date_format = get_company_option('date_format','Y-m-d');
                            return date($date_format, strtotime($project->start_date));
                        })
                        ->editColumn('end_date', function ($project) {
                            $date_format = get_company_option('date_format','Y-m-d');
                            return date($date_format, strtotime($project->end_date));
                        })
                        ->editColumn('status', function ($project) {
                            return project_status($project->status);
                        })
                        ->editColumn('members.name', function ($project) {
                            $members = '';
                            foreach($project->members as $member){
                                $members .= '<img src="'. asset('public/uploads/profile/'.$member->profile_picture) .'" class="project-avatar" data-toggle="tooltip" data-placement="top" title="'. $member->name .'">&nbsp;';
                            }
                            return $members;
                        })
 
                        ->addColumn('action', function ($project) {
                            return '<form action="'.action('ProjectController@destroy', $project['id']).'" class="text-center" method="post">'
                            .'<a href="'.action('ProjectController@show', $project['id']).'" class="btn btn-primary btn-xs"><i class="ti-eye"></i></a>&nbsp;'
                            .'<a href="'.action('ProjectController@edit', $project['id']).'" data-title="'. _lang('Update Project') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>&nbsp;'
                            .csrf_field()
                            .'<input name="_method" type="hidden" value="DELETE">'
                            .'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
                            .'</form>';
                        })
                        ->setRowId(function ($project) {
                          return "row_".$project->id;
                        })
                        ->rawColumns(['action','members.name','status','name'])
                        ->make(true);                 
    }
	
	public function get_project_info( $id = '' ){
  		$project = Project::with('client')
		                  ->where("id",$id)
  					      ->where("company_id",company_id())->first();
  		echo json_encode($project);				  	
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if( ! $request->ajax()){
           return view('backend.accounting.project.create');
        }else{
           return view('backend.accounting.project.modal.create');
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
            'name' => 'required',
            'client_id' => 'required',
            'billing_type' => 'required',
            'status' => 'required',
            'fixed_rate' => 'required_if:billing_type,fixed',
            'hourly_rate' => 'required_if:billing_type,hourly',
            'start_date' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            }else{
                return redirect()->route('projects.create')
                               ->withErrors($validator)
                               ->withInput();
            }     
        }
     
        DB::beginTransaction();

        $project = new Project();
        $project->name = $request->input('name');
        $project->client_id = $request->input('client_id');
        $project->progress = $request->input('progress');
        $project->billing_type = $request->input('billing_type');
        $project->status = $request->input('status');
        $project->fixed_rate = $request->input('fixed_rate');
        $project->hourly_rate = $request->input('hourly_rate');
        $project->start_date = $request->input('start_date');
        $project->end_date = $request->input('end_date');
        $project->description = $request->input('description');
        $project->user_id = Auth::id();
        $project->company_id = company_id();

        $project->save();

        create_log('projects', $project->id, _lang('Created Project'));


        //Store Project Members
        if(isset($request->members)){
            foreach($request->members as $member){
                $project_member  = new ProjectMember();
                $project_member->project_id = $project->id;
                $project_member->user_id = $member;
                $project_member->save();

                create_log('projects', $project->id, _lang('Assign to').' '.$project_member->user->name);
            }
        }


        if($project->client->user->id != null){
           Notification::send($project->client->user, new ProjectCreated($project));
        }
        Notification::send($project->members, new ProjectCreated($project));

        DB::commit();


        if(! $request->ajax()){
           return redirect()->route('projects.create')->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'), 'data'=>$project, 'table' => '#projects_table']);
        }
        
   }
  

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $company_id = company_id();
        $user_type = Auth::user()->user_type;
        $data = array();

        $data['project'] = Project::where('projects.id', $id)
                                  ->where('company_id', $company_id)
                                  ->when($user_type, function ($query, $user_type) {
                                        if($user_type == 'staff'){
                                            $query->whereHas('members', function( $q ) use( $user_type ){
                                                $q->where('user_id', Auth::id());
                                            });
                                        }
                                    })
                                  ->first();
		if(! $data['project']){
			return back()->with('error', _lang('Sorry, Project not found !'));
		}						  
								  
        //get Summary data
        $data['hour_completed'] = \App\TimeSheet::where('project_id',$id)
                                                      ->selectRaw("SUM( TIMESTAMPDIFF(SECOND, start_time, end_time) ) as total_seconds")
                                                      ->where('company_id', $company_id)
                                                      ->first();


        $data['invoices'] = \App\Invoice::where('related_to','projects')
                                        ->where('related_id', $id)
                                        ->where('company_id', $company_id)
                                        ->get();

        $data['expenses'] = \App\Transaction::where("transactions.company_id",$company_id)
                                            ->where("project_id",$id)
                                            ->orderBy("transactions.id","desc")
                                            ->get();

        $data['tasks'] = \App\Task::where('project_id',$id)
                                  ->where('company_id', $company_id)
                                  ->when($user_type, function ($query, $user_type) {
                                        if($user_type == 'staff'){
                                           return $query->where('assigned_user_id',Auth::id());
                                        }
                                    })
                                  ->get();           

        $data['timesheets'] = \App\TimeSheet::where('project_id',$id)
                                            ->where('company_id', $company_id)
                                            ->orderBy('id','desc')
                                            ->get();                     

        $data['project_milestones']  = \App\ProjectMilestone::where('project_id',$id)
                                                            ->where('company_id',$company_id)
                                                            ->orderBy('id','desc')
                                                            ->get();


        $data['projectfiles'] = \App\ProjectFile::where('related_id', $id)
                                                ->where('related_to', 'projects')
                                                ->where('company_id', $company_id)
                                                ->orderBy('id','desc')
                                                ->get();

        $data['notes'] = \App\Note::where('related_id', $id)
                                  ->where('related_to', 'projects')
                                  ->where('company_id', $company_id)
                                  ->orderBy('id','desc')
                                  ->get();                    
        
        return view('backend.accounting.project.view', $data);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $project = Project::where('id',$id)
                          ->where('company_id',company_id())
                          ->first();
        if(! $request->ajax()){
            return view('backend.accounting.project.edit',compact('project','id'));
        }else{
            return view('backend.accounting.project.modal.edit',compact('project','id'));
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
        'name' => 'required',
        'client_id' => 'required',
        'billing_type' => 'required',
        'status' => 'required',
        'fixed_rate' => 'required_if:billing_type,fixed',
        'hourly_rate' => 'required_if:billing_type,hourly',
        'start_date' => 'required',
    ]);

    if ($validator->fails()) {
      if($request->ajax()){ 
        return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
      }else{
        return redirect()->route('projects.edit', $id)
              ->withErrors($validator)
              ->withInput();
      }     
    }
  
          
    DB::beginTransaction();

        $company_id = company_id();

        $project = Project::where('id',$id)
                          ->where('company_id',$company_id)
                          ->first();
        $project->name = $request->input('name');
        $project->client_id = $request->input('client_id');
        $project->progress = $request->input('progress');
        $project->billing_type = $request->input('billing_type');

        if($project->status != $request->input('status')){
        $project->status = $request->input('status');
            create_log('projects', $project->id, _lang('Change Project Status').' - '.ucwords(str_replace('_',' ',$project->status)));
        }
        $project->fixed_rate = $request->input('fixed_rate');
        $project->hourly_rate = $request->input('hourly_rate');
        $project->start_date = $request->input('start_date');
        $project->end_date = $request->input('end_date');
        $project->description = $request->input('description');
        $project->user_id = Auth::id();
        $project->company_id = $company_id;
  
        $project->save();

        create_log('projects', $project->id, _lang('Updated Project'));

        $existing_members  = ProjectMember::where('project_id', $project->id)->get();


        if(isset($request->members)){
            //Remove Project Members
            foreach($existing_members as $existing_member){
                if(! in_array($existing_member->user_id, $request->members)){
                    $project_member = ProjectMember::find($existing_member->id);
                    create_log('projects', $project->id, _lang('Remove').' '.$project_member->user->name.' '._lang('from Project'));
                    $project_member->delete();
                }
            }

            //Store New Project Members
            foreach($request->members as $member){
                if(! $existing_members->contains('user_id', $member)){
                    //Added New Member
                    $project_member  = new ProjectMember();
                    $project_member->project_id = $project->id;
                    $project_member->user_id = $member;
                    $project_member->save();

                    create_log('projects', $project->id, _lang('Assign to').' '.$project_member->user->name);
                }
          
            }
        }else{
             $existing_members  = ProjectMember::where('project_id', $project->id);
             $existing_members->delete();
        }

        if($project->client->user->id != null){
           Notification::send($project->client->user, new ProjectUpdated($project));
        }
        Notification::send($project->members, new ProjectUpdated($project));

        DB::commit();
    
    if(! $request->ajax()){
           return redirect()->route('projects.index')->with('success', _lang('Updated Sucessfully'));
        }else{
       return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'), 'data'=>$project, 'table' => '#projects_table']);
    }
      
    }


     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_project_member(Request $request, $member_id)
    {
        DB::beginTransaction();
        $project_member = ProjectMember::join('projects','projects.id','project_members.project_id')
                                       ->where('project_members.user_id', $member_id)
                                       ->where('company_id',company_id())
                                       ->select('project_members.*')
                                       ->first();
        
        create_log('projects', $project_member->project_id, _lang('Removed').' '.$project_member->user->name.' '._lang('from Project'));

        $project_member->delete();
        DB::commit();

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Member Removed'),'id'=>$member_id, 'table' => '#project_members_table']);
        }
        
    }


    /* Get Logs Data*/
    public function get_logs_data($project_id){
        
        $logs = \App\ActivityLog::with('created_by')
                                ->select('activity_logs.*')
                                ->where("activity_logs.company_id",company_id())
                                ->where('related_to','projects')
                                ->where('related_id',$project_id)
                                ->orderBy("activity_logs.id","desc")
                                ->get();

        echo json_encode($logs);                            
    }

    /**
     * Store File to Project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upload_file(Request $request)
    { 

        $max_size = get_option('file_manager_max_upload_size',2) * 1024;
        $supported_file_types = get_option('file_manager_file_type_supported','png,jpg,jpeg');
         
        $validator = Validator::make($request->all(), [
            'related_id' => 'required',
            'file' => "required|file|max:$max_size|mimes:$supported_file_types",
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return back()->withErrors($validator)
                             ->withInput();
            }            
        }
    
        $file_path = '';
        if($request->hasfile('file'))
        {
            $file = $request->file('file');
            $file_path = time().$file->getClientOriginalName();
            $file->move(public_path()."/uploads/project_files/", $file_path);
        }

        $projectfile = new \App\ProjectFile();
        $projectfile->related_to = 'projects';
        $projectfile->related_id = $request->input('related_id');
        $projectfile->file = $file_path;
        $projectfile->user_id = Auth::id();
        $projectfile->company_id = company_id();

        $projectfile->save();

        create_log('projects', $projectfile->related_id, _lang('Uploaded File'));

        //Prefix output
        $projectfile->file = '<a href="'. url('projects/download_file/'.$projectfile->file) .'">'.$projectfile->file .'</a>';
        $projectfile->user_id = '<a href="'. action('StaffController@show', $projectfile->user->id) .'" data-title="'. _lang('View Staf Information') .'"class="ajax-modal-2">'. $projectfile->user->name .'</a>';
        $projectfile->remove = '<a class="ajax-get-remove" href="'. url('projects/delete_file/'.$projectfile->id) .'">'. _lang('Remove') .'</a>';

        if(! $request->ajax()){
           return back()->with('success', _lang('File Uploaded Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('File Uploaded Sucessfully'),'data'=>$projectfile, 'table' => '#files_table']);
        }
        
   }

   /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_file(Request $request, $id)
    {
        if(Auth::user()->user_type == 'admin'){
            $projectfile = \App\ProjectFile::where($id)
                                           ->where('company_id',$company_id());
            unlink(public_path('uploads/project_files/'.$projectfile->file));
            $projectfile->delete();

            create_log('projects', $id, _lang('File Removed'));
        }

        if(Auth::user()->user_type != 'admin'){
            $projectfile = \App\ProjectFile::where('id',$id)
                                           ->where('user_id',Auth::id())
                                           ->first();
            if(!$projectfile){
                if(! $request->ajax()){
                   return back()->with('error',_lang('Sorry only admin or creator can remove this file !'));
                }else{
                   return response()->json(['result'=>'error','message'=>_lang('Sorry only admin or creator can remove this file !')]);
                }

            }                              
            unlink(public_path('uploads/project_files/'.$projectfile->file));
            $projectfile->delete();

            create_log('projects', $id, _lang('File Removed'));
        }

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Removed Sucessfully'),'id'=>$id, 'table' => '#files_table']);
        }
        
    }

    public function download_file(Request $request, $file){
        $file = 'public/uploads/project_files/'.$file;
        return response()->download($file);
    }

    /**
     * Store note.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create_note(Request $request)
    {    
        $validator = Validator::make($request->all(), [
            'related_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('notes.create')
                                 ->withErrors($validator)
                                 ->withInput();
            }            
        }
      
        $note = new \App\Note();
        $note->related_to ='projects';
        $note->related_id = $request->input('related_id');
        $note->note = $request->input('note');
        $note->user_id = Auth::id();
        $note->company_id = company_id();

        $note->save();

        create_log('projects', $note->related_id, _lang('Added Note'));

        //Prefix Output
        $note->created = '<small>'.$note->user->name.'('.$note->created_at.')<br>'.$note->note.'</small>';
        $note->action = '<a href="'. url('projects/delete_note/'.$note->id) .'" class="ajax-get-remove"><i class="far fa-trash-alt text-danger"></i></a>';

        if(! $request->ajax()){
           return back()->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$note, 'table' => '#notes_table']);
        }
        
   }

   /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_note(Request $request, $id)
    {
        if(Auth::user()->user_type == 'admin'){
            $note = \App\Note::where('id', $id)
                             ->where('company_id', company_id());
            $note->delete();
            create_log('projects', $id, _lang('Removed Note'));
        }

        if(Auth::user()->user_type != 'admin'){
            $note = \App\Note::where('id',$id)
                             ->where('user_id',Auth::id())
                             ->first();
            if(!$note){
                if(! $request->ajax()){
                   return back()->with('error',_lang('Sorry only admin or creator can remove this file !'));
                }else{
                   return response()->json(['result'=>'error','message'=>_lang('Sorry only admin or creator can remove this file !')]);
                }

            }                              
            $note->delete();
            create_log('projects', $id, _lang('Removed Note'));
        }

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Removed Sucessfully'),'id'=>$id, 'table' => '#notes_table']);
        }
        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){

        DB::beginTransaction();

        $company_id = company_id();

        $project = $project = Project::where('id',$id)
                                     ->where('company_id',$company_id);
        $project->delete();

        $invoice = \App\Invoice::where('related_to','projects')
                                ->where('related_id', $id)
                                ->where('company_id', $company_id);
        $invoice->delete();


        $project_milestones  = \App\ProjectMilestone::where('company_id', $company_id)
                                                    ->where('project_id', $id);
        $project_milestones->delete();


        $projectfiles = \App\ProjectFile::where('related_id', $id)
                                        ->where('related_to', 'projects')
                                        ->where('company_id', $company_id);
        $projectfiles->delete();


        $notes = \App\Note::where('related_id', $id)
                          ->where('related_to', 'projects')
                          ->where('company_id', $company_id);     
        $notes->delete();

        DB::commit();
        
  
        return redirect()->route('projects.index')->with('success',_lang('Deleted Sucessfully'));
    }
}