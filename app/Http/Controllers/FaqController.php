<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Faq;
use Validator;
use Illuminate\Validation\Rule;

class FaqController extends Controller
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
        $faqs = Faq::all()->sortByDesc("id");
        return view('backend.faq.list',compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.faq.create');
		}else{
           return view('backend.faq.modal.create');
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
			'question.*' => 'required|string',
			'answer.*' => 'required|string'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('faqs.create')
							->withErrors($validator)
							->withInput();
			}			
		}
			
		
        $faq = new Faq();
	    $faq->question = serialize($request->question);
		$faq->answer = serialize($request->answer);
	
        $faq->save();
        
		if(! $request->ajax()){
           return redirect()->route('faqs.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$faq]);
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
        $faq = Faq::find($id);
		if(! $request->ajax()){
		    return view('backend.faq.view',compact('faq','id'));
		}else{
			return view('backend.faq.modal.view',compact('faq','id'));
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
        $faq = Faq::find($id);
		if(! $request->ajax()){
		   return view('backend.faq.edit',compact('faq','id'));
		}else{
           return view('backend.faq.modal.edit',compact('faq','id'));
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
			'question.*' => 'required',
			'answer.*' => 'required'
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('faqs.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $faq = Faq::find($id);
		$faq->question = serialize($request->question);
        $faq->answer = serialize($request->answer);
	
        $faq->save();
		
		if(! $request->ajax()){
           return redirect()->route('faqs.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$faq]);
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
        $faq = Faq::find($id);
        $faq->delete();
        return redirect()->route('faqs.index')->with('success',_lang('Deleted Sucessfully'));
    }
}
