<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lead;
use App\LeadStatus;
use App\User;
use App\Contact;
use Validator;
use DataTables;
use Auth;
use DB;
use Illuminate\Validation\Rule;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
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

            $lead_status = LeadStatus::where('company_id', $company_id)
                                     ->orderBy('order','asc')
                                     ->get();                                   
            return view('backend.accounting.lead.kanban_view',compact('lead_status'));
        }else{
            return view('backend.accounting.lead.list');
        }
    }

    /* Get Logs Data*/
    public function load_more_lead(Request $request, $lead_status_id, $last_lead_id){

        if( $request->ajax()){
            $leads = Lead::where('company_id', company_id())
                         ->where('lead_status_id',$lead_status_id)
                         ->where('id','<', $last_lead_id)
                         ->with(['assigned_user','lead_source'])
                         ->latest()
                         ->limit(20)
                         ->get();
            echo json_encode($leads);  
        }                                      
    }

    /** Update Lead Status **/
    public function update_lead_status(Request $request, $lead_status_id, $lead_id){

        if( $request->ajax()){
            $lead = Lead::where('id',$lead_id)
                        ->where('company_id', company_id())
                        ->first();
             
            if($lead){
               $lead->lead_status_id = $lead_status_id;
               $lead->save();
               echo json_encode($lead); 
            } 
        }                                      
    }

    public function get_table_data(Request $request){
        
        $user_type = Auth::user()->user_type;

        $leads = Lead::with('lead_status')
                     ->with('lead_source')
                     ->with('assigned_user')
                     ->select('leads.*')
                     ->where("leads.company_id",company_id())
                     ->when($user_type, function ($query, $user_type) {
                        if($user_type == 'staff'){
                           return $query->where('assigned_user_id', Auth::id());
                        }
                     })
                     ->orderBy("leads.id","desc");

        return Datatables::eloquent($leads)
                        ->filter(function ($query) use ($request) {
                            if ($request->has('assigned_user_id')) {
                                $query->where('assigned_user_id', 'like', "%{$request->post('assigned_user_id')}%");
                            }

                            if ($request->has('lead_status_id')) {
                                $query->whereIn('lead_status_id', json_decode($request->post('lead_status_id')));
                            }

                            if ($request->has('lead_source_id')) {
                                $query->where('lead_source_id', 'like', "%{$request->post('lead_source_id')}%");
                            }

                            if ($request->has('country')) {
                                $query->where('country', 'like', "%{$request->post('country')}%");
                            }
                        })
                        ->editColumn('name', function ($lead) {
                            return '<a href="'.action('LeadController@show', $lead->id).'" data-title="'. _lang('View Lead Details') .'" class="ajax-modal">'.$lead->name.'</a>';
                        })
                        ->editColumn('contact_date', function ($lead) {
                            $date_format = get_company_option('date_format','Y-m-d');
                            return date($date_format, strtotime($lead->contact_date));
                        })
                        ->editColumn('lead_status.title', function ($lead) {
                            $status_color = $lead->lead_status->color;
                            return "<span class='badge badge-primary' style='background:{$status_color}'>{$lead->lead_status->title}</span>";
                        })
                        ->addColumn('action', function ($lead) {
                                return '<form action="'.action('LeadController@destroy', $lead['id']).'" class="text-center" method="post">'
                                .'<a href="'.action('LeadController@show', $lead['id']).'" data-title="'. _lang('View Lead Details') .'" class="btn btn-primary btn-xs ajax-modal"><i class="ti-eye"></i></a>&nbsp;'
                                .'<a href="'.action('LeadController@edit', $lead['id']).'" data-title="'. _lang('Update Lead') .'" class="btn btn-warning btn-xs ajax-modal"><i class="ti-pencil"></i></a>&nbsp;'
                                .csrf_field()
                                .'<input name="_method" type="hidden" value="DELETE">'
                                .'<button class="btn btn-danger btn-xs btn-remove" type="submit"><i class="ti-eraser"></i></button>'
                                .'</form>';
                        })
                        ->setRowId(function ($lead) {
                            return "row_".$lead->id;
                        })
                        ->rawColumns(['action','lead_status.title','name'])
                        ->make(true);                                
    }

    /* Get Logs Data*/
    public function get_logs_data($lead_id){
        
        $logs = \App\ActivityLog::with('created_by')
                                ->select('activity_logs.*')
                                ->where("activity_logs.company_id",company_id())
                                ->where('related_to','leads')
                                ->where('related_id',$lead_id)
                                ->orderBy("activity_logs.id","desc")
                                ->get();

        echo json_encode($logs);                            
    }

    /** Import Lead **/
    public function import(Request $request)
    {       
        if($request->isMethod('get')){
            return view('backend.accounting.lead.import');
        }else{
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $validator = Validator::make($request->all(), [
                'lead_status_id'    => 'required',
                'lead_source_id'    => 'required',
                'assigned_user_id'  => 'required',
                'file'              => 'required|mimes:xlsx',
            ]);
            
            if ($validator->fails()) {
                if($request->ajax()){ 
                    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
                }else{
                    return redirect('leads/import')->withErrors($validator)
                                                      ->withInput();
                }           
            }
                
            //Import Contacts
            //$file_type = $request->file('file')->getClientOriginalExtension();
            $new_rows = 0;

            DB::beginTransaction();
            
            $previous_rows = Lead::where('company_id',company_id())->count();

            $data = array();
            $data['lead_status_id'] = $request->lead_status_id;
            $data['lead_source_id'] = $request->lead_source_id;
            $data['assigned_user_id'] = $request->assigned_user_id;

            $import = Excel::import(new LeadsImport($data), request()->file('file'));

            $current_rows = Lead::where('company_id',company_id())->count();

            $new_rows = $current_rows - $previous_rows;

            DB::commit();

            return back()->with('success',$new_rows.' '._lang('Rows Imported Sucessfully'));
        }           
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if( ! $request->ajax()){
           return view('backend.accounting.lead.create');
        }else{
           return view('backend.accounting.lead.modal.create');
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
          'company_name' => 'nullable|max:50',
          'email' => [
              'nullable',
              'email',
              Rule::unique('leads')->where('company_id',company_id()),
          ],
    			'lead_status_id' => 'required',
    			'lead_source_id' => 'required',
    			'assigned_user_id' => 'required',
    			'contact_date' => 'required',
    			'phone' => 'nullable|max:20',
    			'website' => 'nullable|max:191',
    			'country' => 'nullable|max:50',
    			'currency' => 'required|max:3',
        ]);

        if ($validator->fails()) {
            if($request->ajax()){ 
                return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
            }else{
                return redirect()->route('leads.create')
                	             ->withErrors($validator)
                	             ->withInput();
            }			
        }
	 

        $lead = new Lead();
        $lead->name = $request->input('name');
        $lead->company_name = $request->input('company_name');
    		$lead->email = $request->input('email');
    		$lead->lead_status_id = $request->input('lead_status_id');
    		$lead->lead_source_id = $request->input('lead_source_id');
    		$lead->assigned_user_id = $request->input('assigned_user_id');
    		$lead->created_user_id = Auth::id();
    		$lead->contact_date = $request->input('contact_date');
    		$lead->phone = $request->input('phone');
    		$lead->website = $request->input('website');
    		$lead->country = $request->input('country');
    		$lead->currency = $request->input('currency');
    		$lead->vat_id = $request->input('vat_id');
    		$lead->reg_no = $request->input('reg_no');
    		$lead->city = $request->input('city');
    		$lead->state = $request->input('state');
    		$lead->zip = $request->input('zip');
    		$lead->address = $request->input('address');
    		$lead->custom_fields = $request->input('custom_fields');
    		$lead->company_id = company_id();

        $lead->save();

        create_log('leads', $lead->id, _lang('Created Lead'));
        create_log('leads', $lead->id, _lang('Assign to').' '.$lead->assigned_user->name);

        if(! $request->ajax()){
           return redirect()->route('leads.create')->with('success', _lang('Saved Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$lead]);
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
        $company_id = company_id();

        $data['lead'] = Lead::find($id);
        $data['quotations'] = \App\Quotation::where('related_id',$id)
                                            ->where('related_to', 'leads')
                                            ->where('company_id', $company_id)
                                            ->orderBy('id','desc')
                                            ->get();

        $data['projectfiles'] = \App\ProjectFile::where('related_id',$id)
                                                ->where('related_to', 'leads')
                                                ->where('company_id', $company_id)
                                                ->orderBy('id','desc')
                                                ->get();

        $data['notes'] = \App\Note::where('related_id',$id)
                                  ->where('related_to', 'leads')
                                  ->where('company_id', $company_id)
                                  ->orderBy('id','desc')
                                  ->get();                                                               

        if(! $request->ajax()){
            return view('backend.accounting.lead.view', $data);
        }else{
            return view('backend.accounting.lead.modal.view', $data);
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
        $lead = Lead::where('id', $id)
                    ->where('company_id', company_id())
                    ->first();

        if(! $request->ajax()){
            return view('backend.accounting.lead.edit',compact('lead','id'));
        }else{
            return view('backend.accounting.lead.modal.edit',compact('lead','id'));
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
        DB::beginTransaction();

        $company_id = company_id();

        $lead = Lead::where('id', $id)
                    ->where('company_id', $company_id)
                    ->first();

    	$validator = Validator::make($request->all(), [
    		'name' => 'required|max:50',
    		'company_name' => 'nullable|max:50',
			'email' => [
            'nullable',
            'email',
            Rule::unique('leads')->where('company_id', $company_id)->ignore($lead->id),
          ],
			'lead_status_id' => 'required',
			'lead_source_id' => 'required',
			'assigned_user_id' => 'required',
			'contact_date' => 'required',
			'phone' => 'nullable|max:20',
			'website' => 'nullable|max:191',
			'country' => 'nullable|max:50',
			'currency' => 'required|max:3',
		]);

		if ($validator->fails()) {
			if($request->ajax()){ 
				return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('leads.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
			
		
		$lead->name = $request->input('name');
		$lead->company_name = $request->input('company_name');
		$lead->email = $request->input('email');
		$lead->lead_status_id = $request->input('lead_status_id');
		$lead->lead_source_id = $request->input('lead_source_id');
            
        if($lead->assigned_user_id != $request->assigned_user_id){
            $lead->assigned_user_id = $request->input('assigned_user_id');
            create_log('leads', $lead->id, _lang('Assign to').' '.$lead->assigned_user->name);
        }
    		
    		$lead->contact_date = $request->input('contact_date');
    		$lead->phone = $request->input('phone');
    		$lead->website = $request->input('website');
    		$lead->country = $request->input('country');
    		$lead->currency = $request->input('currency');
    		$lead->vat_id = $request->input('vat_id');
    		$lead->reg_no = $request->input('reg_no');
    		$lead->city = $request->input('city');
    		$lead->state = $request->input('state');
    		$lead->zip = $request->input('zip');
    		$lead->address = $request->input('address');
    		$lead->custom_fields = $request->input('custom_fields');
    		$lead->company_id = $company_id;
    	
        $lead->save();

        create_log('leads', $lead->id, _lang('Updated Lead'));

        DB::commit();
    		
    		if(! $request->ajax()){
               return redirect()->route('leads.index')->with('success', _lang('Updated Sucessfully'));
            }else{
    		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$lead]);
    		}
	    
    }
	
	  public function get_lead_info( $id = '' ){
  		$lead = Lead::where("id",$id)
  					      ->where("company_id",company_id())->first();
  		echo json_encode($lead);				  	
	  }

     /**
     * Store File to Lead.
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
        $projectfile->related_to = 'leads';
        $projectfile->related_id = $request->input('related_id');
        $projectfile->file = $file_path;
        $projectfile->user_id = Auth::id();
        $projectfile->company_id = company_id();

        $projectfile->save();

        create_log('leads', $projectfile->related_id, _lang('Uploaded File'));

        //Prefix output
        $projectfile->file = '<a href="'. url('leads/download_file/'.$projectfile->file) .'">'.$projectfile->file .'</a>';
        $projectfile->user_id = '<a href="'. action('StaffController@show', $projectfile->user->id) .'" data-title="'. _lang('View Staf Information') .'"class="ajax-modal-2">'. $projectfile->user->name .'</a>';
        $projectfile->remove = '<a class="ajax-get-remove" href="'. url('leads/delete_file/'.$projectfile->id) .'">'. _lang('Remove') .'</a>';

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

            create_log('leads', $id, _lang('File Removed'));
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

            create_log('leads', $id, _lang('File Removed'));
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
        $note->related_to ='leads';
        $note->related_id = $request->input('related_id');
        $note->note = $request->input('note');
        $note->user_id = Auth::id();
        $note->company_id = company_id();

        $note->save();

        create_log('leads', $note->related_id, _lang('Added Note'));

        //Prefix Output
        $note->created = '<small><a href="'. action('StaffController@show', $note->user->id) .'" data-title="'. _lang('View Staf Information') .'" class="ajax-modal-2">'.$note->user->name.'</a>('.$note->created_at.')<br>'.$note->note.'</small>';
        $note->action = '<a href="'. url('leads/delete_note/'.$note->id) .'" class="ajax-get-remove"><i class="far fa-trash-alt text-danger"></i></a>';

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
            create_log('leads', $id, _lang('Removed Note'));
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
            create_log('leads', $id, _lang('Removed Note'));
        }

        if(! $request->ajax()){
           return back()->with('success',_lang('Removed Sucessfully'));
        }else{
           return response()->json(['result'=>'success','action'=>'delete','message'=>_lang('Removed Sucessfully'),'id'=>$id, 'table' => '#notes_table']);
        }
        
    }


    public function convert_to_customer(Request $request, $lead_id){

        if($request->isMethod('get')){
            $data = array();
            $data['lead'] = Lead::where('id',$lead_id)
                                ->where('converted_lead', null)
                                ->where('company_id', company_id())
                                ->first();
            return view('backend.accounting.lead.convert_to_customer', $data);
        }else{
            $validator = Validator::make($request->all(), [
                'profile_type'  => 'required|max:20',
                'company_name'  => 'nullable|max:50',
                'contact_name'  => 'required|max:50',
                'contact_email' => [
                    'required',
                    'email',
                    Rule::unique('contacts')->where('company_id',company_id()),
                ],
                'contact_phone' => 'nullable|max:20',
                'country'       => 'nullable|max:50',
                'currency'      => 'required|max:3',
                'city'          => 'nullable|max:50',
                'state'         => 'nullable|max:50',
                'zip'           => 'nullable|max:20',
                'contact_image' => 'nullable|image||max:5120',
                'group_id'      => 'required',
                'lead_id'      => 'required',
            ],[
                'group_id.required' => 'The group field is required.' 
            ]);
            
            if ($validator->fails()) {
                if($request->ajax()){ 
                    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
                }else{
                    return back()->withErrors($validator)
                                 ->withInput();
                }           
            }
            
            $contact_image ="avatar.png";       
            if($request->hasfile('contact_image'))
            {
                 $file = $request->file('contact_image');
                 $contact_image = "contact_image".time().'.'.$file->getClientOriginalExtension();
                 $file->move(public_path()."/uploads/contacts/", $contact_image);
            }

            
            DB::beginTransaction();

            //Check if lead ID is valid
            $lead = Lead::where('id',$request->lead_id)
                        ->where('company_id',company_id())
                        ->first();

            if( ! $lead ){
                if($request->ajax()){ 
                    return response()->json(['result'=>'error','message'=>'Invalid Lead !']);
                }else{
                    return back()->with('error', _lang('Invalid Lead !'))->withInput();
                }  
            }            

            //Check client has already an account
            $other = User::where('email',$request->contact_email)
                         ->where('user_type','!=','client')->first();

            if( $other ){
                if($request->ajax()){ 
                    return response()->json(['result'=>'error','message'=>'Sorry, This email already registered with an company admin or staff !']);
                }else{
                    return back()->with('error', _lang('Sorry, This email already registered with an company admin or staff !'))->withInput();
                }   
            }

            $client = User::where('email',$request->contact_email)
                          ->where('user_type','client')->first();
            

            $contact = new Contact();
            $contact->profile_type = $request->input('profile_type');
            $contact->company_name = $request->input('company_name');
            $contact->contact_name = $request->input('contact_name');
            $contact->contact_email = $request->input('contact_email');
            $contact->contact_phone = $request->input('contact_phone');
            $contact->vat_id = $request->input('vat_id');
            $contact->reg_no = $request->input('reg_no');
            $contact->country = $request->input('country');
            $contact->currency = $request->input('currency');
            $contact->city = $request->input('city');
            $contact->state = $request->input('state');
            $contact->zip = $request->input('zip');
            $contact->address = $request->input('address');
            $contact->facebook = $request->input('facebook');
            $contact->twitter = $request->input('twitter');
            $contact->linkedin = $request->input('linkedin');
            $contact->remarks = $request->input('remarks');
            if($client){
                $contact->user_id = $client->id;
            }
            $contact->group_id = $request->input('group_id');
            $contact->company_id = company_id();
            $contact->contact_image = $contact_image;
        
            $contact->save();

            //Update Lead 
            $lead->converted_lead = 1;
            $lead->save();
            
            //Update Package limit
            update_package_limit('contacts_limit');
            
            create_log('leads', $lead_id, _lang('Converted Lead to Customer'));

            DB::commit();
            

            if(! $request->ajax()){
               return redirect('contacts/'.$contact->id)->with('success', _lang('Lead converted sucessfully'));
            }else{
               return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Lead converted sucessfully'),'data' => $contact]);
            }
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
        DB::beginTransaction();

        $company_id = company_id();

        $lead = Lead::where('id',$id)
                    ->where('company_id',$company_id);
        $lead->delete();

        $quotations = \App\Quotation::where('related_to','leads')
                                    ->where('related_id', $id)
                                    ->where('company_id', $company_id);
        $quotations->delete();


        $projectfiles = \App\ProjectFile::where('related_id', $id)
                                        ->where('related_to', 'leads')
                                        ->where('company_id', $company_id);
        $projectfiles->delete();


        $notes = \App\Note::where('related_id', $id)
                          ->where('related_to', 'leads')
                          ->where('company_id', $company_id);     
        $notes->delete();

        return redirect()->route('leads.index')->with('success',_lang('Deleted Sucessfully'));
    }
}