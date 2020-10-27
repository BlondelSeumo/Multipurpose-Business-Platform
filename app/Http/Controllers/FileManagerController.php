<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FileManager;
use Validator;
use Illuminate\Validation\Rule;
use Auth;

class FileManagerController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
		
		$this->middleware(function ($request, $next) {
            if( has_membership_system() == 'enabled' ){
                if( ! has_feature( 'file_manager' ) ){
                    return redirect('membership/extend')->with('message', _lang('Your Current package not support this feature. You can upgrade your package !'));
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
    public function index($parent_id = '')
    {
		if($parent_id==''){
			$back = false;
			$filemanagers = FileManager::where("company_id",company_id())
									   ->where('parent_id',null)
									   ->orderBy("name","asc")
									   ->get();
		}else{
			$back = true;
			$parent_id = decrypt($parent_id);
			$filemanagers = FileManager::where("company_id",company_id())
									   ->where('parent_id',$parent_id)
									   ->orderBy("name","asc")
									   ->get();
		}						   
        return view('backend.file_manager.list',compact('filemanagers','back'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $parent_id='')
    {
		$parent_directory = FileManager::where('is_dir','yes')
		                               ->where('company_id',company_id())
									   ->get();
		if( ! $request->ajax()){
		   return view('backend.file_manager.create',compact('parent_directory','parent_id'));
		}else{
           return view('backend.file_manager.modal.create',compact('parent_directory','parent_id'));
		}
    }
	
	/**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_folder(Request $request, $parent_id='')
    {
		$parent_directory = FileManager::where('is_dir','yes')
		                               ->where('company_id',company_id())
									   ->get();
		if( ! $request->ajax()){
		   return view('backend.file_manager.create_folder',compact('parent_directory','parent_id'));
		}else{
           return view('backend.file_manager.modal.create_folder',compact('parent_directory','parent_id'));
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
		$max_size = get_option('file_manager_max_upload_size',2) * 1024;
		$supported_file_types = get_option('file_manager_file_type_supported','png,jpg,jpeg');
		
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:64',
			'file' => "required|file|max:$max_size|mimes:$supported_file_types",
		],
		[
		    'mimes' => 'File type is not supported',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('file_manager.create')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		if($this->is_duplicate_file($request->input('name'), $request->input('parent_id'))){
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>array('error'=> _lang('File Name already exists !'))]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}	
		}
			
	    if($request->hasfile('file'))
		{
		 $file = $request->file('file');
		 $file_name = time().$file->getClientOriginalName();
		 $file->move(public_path()."/uploads/file_manager/", $file_name);
		}
		
        $filemanager = new FileManager();
	    $filemanager->name = $request->input('name');
		$filemanager->mime_type = mime_content_type(public_path().'/uploads/file_manager/'.$file_name);
		$filemanager->file = $file_name;
		$filemanager->parent_id = $request->input('parent_id');
		$filemanager->company_id = company_id();
		$filemanager->created_by = Auth::user()->id;
	
        $filemanager->save();

		if(! $request->ajax()){
           return redirect()->route('file_manager.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$filemanager]);
		}
        
   }
   
   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_folder(Request $request)
    {	
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:64',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('file_manager.create_folder')
							->withErrors($validator)
							->withInput();
			}			
		}
		
		if($this->is_duplicate_folder($request->input('name'), $request->input('parent_id'))){
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>array('error'=> _lang('Folder Name already exists !'))]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}	
		}
			
		
        $filemanager = new FileManager();
	    $filemanager->name = $request->input('name');
		$filemanager->is_dir = 'yes';
		$filemanager->parent_id = $request->input('parent_id');
		$filemanager->company_id = company_id();
		$filemanager->created_by = Auth::user()->id;
	
        $filemanager->save();

		if(! $request->ajax()){
           return back()->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$filemanager]);
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
		$parent_directory = FileManager::where('is_dir','yes')
		                               ->where('company_id',company_id())
									   ->get();
									   
        $filemanager = FileManager::where("id",$id)
                                  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.file_manager.edit',compact('filemanager','id','parent_directory'));
		}else{
           return view('backend.file_manager.modal.edit',compact('filemanager','id','parent_directory'));
		}  
        
    }
	
	
	/**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit_folder(Request $request, $id)
    {
		$parent_directory = FileManager::where('is_dir','yes')
		                               ->where('company_id',company_id())
		                               ->where('id','!=',$id)
									   ->get();
									   
        $filemanager = FileManager::where("id",$id)
                                  ->where("company_id",company_id())->first();
		if(! $request->ajax()){
		   return view('backend.file_manager.edit_folder',compact('filemanager','id','parent_directory'));
		}else{
           return view('backend.file_manager.modal.edit_folder',compact('filemanager','id','parent_directory'));
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
		$max_size = get_option('file_manager_max_upload_size',2) * 1024;
		$supported_file_types = get_option('file_manager_file_type_supported','png,jpg,jpeg');
		
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:64',
			'file' => "nullable|file|max:$max_size|mimes:$supported_file_types",
		],
		[
		    'mimes' => 'File type is not supported',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('file_manager.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		if($this->is_duplicate_file($request->input('name'), $request->input('parent_id'), $id)){
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>array('error'=> _lang('File Name already exists !'))]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}	
		}
	
        if($request->hasfile('file'))
		{
			$file = $request->file('file');
			$file_name = time().$file->getClientOriginalName();
			$file->move(public_path()."/uploads/file_manager/", $file_name);
		}	
		
        $filemanager = FileManager::where("id",$id)
                                  ->where("company_id",company_id())->first();
		$filemanager->name = $request->input('name');
		if($request->hasfile('file')){
			$filemanager->file = $file_name;
			$filemanager->mime_type = mime_content_type(public_path().'/uploads/file_manager/'.$file_name);
		}
		$filemanager->parent_id = $request->input('parent_id');
		$filemanager->company_id = company_id();
		$filemanager->created_by = Auth::user()->id;
	
        $filemanager->save();
		
		if(! $request->ajax()){
           return redirect()->route('file_manager.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$filemanager]);
		}
	    
    }
	
	 /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_folder(Request $request, $id)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:64',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('file_manager.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
		
		if($this->is_duplicate_folder($request->input('name'), $request->input('parent_id'), $id)){
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>array('error'=> _lang('Folder Name already exists !'))]);
			}else{
				return back()->withErrors($validator)
							 ->withInput();
			}	
		}
	
        $filemanager = FileManager::where("id",$id)
                                  ->where("company_id",company_id())->first();
		$filemanager->name = $request->input('name');
		$filemanager->parent_id = $request->input('parent_id');
		$filemanager->company_id = company_id();
		$filemanager->created_by = Auth::user()->id;
	
        $filemanager->save();
		
		if(! $request->ajax()){
           return back()->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$filemanager]);
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
        $filemanager = FileManager::where("id",$id)
		                          ->where("company_id",company_id())
								  ->first();
								  
		$parent_files = FileManager::where("parent_id",$filemanager->id)
								   ->where("company_id",company_id());					  
        $parent_files->delete();
        $filemanager->delete();
		
		
        return redirect()->route('file_manager.index')->with('success',_lang('Deleted Sucessfully'));
    }
	
	
	private function is_duplicate_file($name, $parent_id, $ignore_id=''){
		$file = FileManager::where("name",$name)
						   ->where("parent_id",$parent_id)	
						   ->where("is_dir","no")	
						   ->where("id","!=",$ignore_id)	
						   ->where("company_id",company_id());	
	    if( $file->exists() ){
			return true;
		}
		return false;
	}
	
	private function is_duplicate_folder($name, $parent_id, $ignore_id=''){
		$file = FileManager::where("name",$name)
						   ->where("parent_id",$parent_id)	
						   ->where("is_dir","yes")
						   ->where("id","!=",$ignore_id)	
						   ->where("company_id",company_id());	
	    if( $file->exists() ){
			return true;
		}
		return false;
	}
}