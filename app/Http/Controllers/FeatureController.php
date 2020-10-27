<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feature;
use Validator;
use Illuminate\Validation\Rule;

class FeatureController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		date_default_timezone_set(get_option('timezone','Asia/Dhaka'));
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $features = Feature::all()->sortByDesc("id");
        return view('backend.feature.list',compact('features'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.feature.create');
		}else{
           return view('backend.feature.modal.create');
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
			'icon.*' => 'required|string',
			'title.*' => 'required|string',
			'content.*' => 'required|string'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('features.create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
	    
		
        $feature = new Feature();
	    $feature->icon = serialize($request->input('icon'));
	    $feature->title = serialize($request->input('title'));
	    $feature->content = serialize($request->input('content'));
	
        $feature->save();
        
		if(! $request->ajax()){
           return redirect()->route('features.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$feature]);
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
        $feature = Feature::find($id);
		if(! $request->ajax()){
		    return view('backend.feature.view',compact('feature','id'));
		}else{
			return view('backend.feature.modal.view',compact('feature','id'));
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
        $feature = Feature::find($id);
		if(! $request->ajax()){
		   return view('backend.feature.edit',compact('feature','id'));
		}else{
           return view('backend.feature.modal.edit',compact('feature','id'));
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
            'icon.*' => 'required|string',
            'title.*' => 'required|string',
            'content.*' => 'required|string'
        ]);
        
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('features.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $feature = Feature::find($id);
		$feature->icon = serialize($request->input('icon'));
	    $feature->title = serialize($request->input('title'));
	    $feature->content = serialize($request->input('content'));
	
        $feature->save();
		
		if(! $request->ajax()){
           return redirect()->route('features.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$feature]);
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
        $feature = Feature::find($id);
        $feature->delete();
        return redirect()->route('features.index')->with('success',_lang('Deleted Sucessfully'));
    }
}
