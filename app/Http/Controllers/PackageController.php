<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Package;
use Validator;
use Illuminate\Validation\Rule;

class PackageController extends Controller
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
        $packages = Package::all();
        return view('backend.package.list',compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
		if( ! $request->ajax()){
		   return view('backend.package.create');
		}else{
           return view('backend.package.modal.create');
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
			'package_name' => 'required|max:50',
			'is_featured' => 'required',
			'staff_limit' => 'required',
			'contacts_limit' => 'required',
			'invoice_limit' => 'required',
			'quotation_limit' => 'required',
			'live_chat' => 'required',
			'file_manager' => 'required',
			'online_payment' => 'required',
			'cost_per_month' => 'required|numeric',
			'cost_per_year' => 'required|numeric',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('packages.create')
							->withErrors($validator)
							->withInput();
			}			
		}
			

        $package = new Package();
	    $package->package_name = $request->package_name;
		$package->is_featured = $request->is_featured;
		$package->staff_limit = serialize($request->staff_limit);
		$package->contacts_limit = serialize($request->contacts_limit);
		$package->invoice_limit = serialize($request->invoice_limit);
		$package->quotation_limit = serialize($request->quotation_limit);
		$package->project_management_module = serialize($request->project_management_module);
		$package->recurring_transaction = serialize($request->recurring_transaction);
		$package->live_chat = serialize($request->live_chat);
		$package->file_manager = serialize($request->file_manager);
		$package->inventory_module = serialize($request->inventory_module);
		$package->online_payment = serialize($request->online_payment);
		$package->cost_per_month = $request->cost_per_month;
		$package->cost_per_year = $request->cost_per_year;
		//$package->others = $request->others;
	
        $package->save();
        
		if(! $request->ajax()){
           return redirect()->route('packages.create')->with('success', _lang('Saved Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'store','message'=>_lang('Saved Sucessfully'),'data'=>$package]);
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
        $package = Package::find($id);
		if(! $request->ajax()){
		    return view('backend.package.view',compact('package','id'));
		}else{
			return view('backend.package.modal.view',compact('package','id'));
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
        $package = Package::find($id);
		if(! $request->ajax()){
		   return view('backend.package.edit',compact('package','id'));
		}else{
           return view('backend.package.modal.edit',compact('package','id'));
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
			'package_name' => 'required|max:50',
			'is_featured' => 'required',
			'staff_limit' => 'required',
			'contacts_limit' => 'required',
			'invoice_limit' => 'required',
			'quotation_limit' => 'required',
			'live_chat' => 'required',
			'file_manager' => 'required',
			'online_payment' => 'required',
			'cost_per_month' => 'required|numeric',
			'cost_per_year' => 'required|numeric',
		]);
		
		if ($validator->fails()) {
			if($request->ajax()){ 
			    return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
			}else{
				return redirect()->route('packages.edit', $id)
							->withErrors($validator)
							->withInput();
			}			
		}
	
        	
		
        $package = Package::find($id);
		$package->package_name = $request->package_name;
		$package->is_featured = $request->is_featured;
		$package->staff_limit = serialize($request->staff_limit);
		$package->contacts_limit = serialize($request->contacts_limit);
		$package->invoice_limit = serialize($request->invoice_limit);
		$package->quotation_limit = serialize($request->quotation_limit);
		$package->project_management_module = serialize($request->project_management_module);
		$package->recurring_transaction = serialize($request->recurring_transaction);
		$package->live_chat = serialize($request->live_chat);
		$package->file_manager = serialize($request->file_manager);
		$package->inventory_module = serialize($request->inventory_module);
		$package->online_payment = serialize($request->online_payment);
		$package->cost_per_month = $request->cost_per_month;
		$package->cost_per_year = $request->cost_per_year;
		//$package->others = $request->others;
	
        $package->save();
		
		if(! $request->ajax()){
           return redirect()->route('packages.index')->with('success', _lang('Updated Sucessfully'));
        }else{
		   return response()->json(['result'=>'success','action'=>'update', 'message'=>_lang('Updated Sucessfully'),'data'=>$package]);
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
        $package = Package::find($id);
        $package->delete();
        return redirect()->route('packages.index')->with('success',_lang('Deleted Sucessfully'));
    }
}
