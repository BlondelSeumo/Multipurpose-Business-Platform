<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CompanySetting;
use Carbon\Carbon;
use DB;

class CompanySettingsController extends Controller
{
	public function __construct(){
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
	} 
	 
    public function settings($store="",Request $request)
    {
		if($store == ""){
           return view('backend.accounting.general_settings.settings');
        }else{
            $company_id = company_id();			
		    foreach($_POST as $key => $value){
				 if($key == "_token"){
					 continue;
				 }
				 
				 $data = array();
				 $data['value'] = $value; 
				 $data['company_id'] = $company_id; 
				 $data['updated_at'] = Carbon::now();
				 
				 if(CompanySetting::where('name', $key)->where("company_id",$company_id)->exists()){				
					CompanySetting::where('name','=',$key)
					              ->where("company_id",$company_id)
								  ->update($data);			
				 }else{
					$data['name'] = $key; 
					$data['created_at'] = Carbon::now();
					CompanySetting::insert($data); 
				 }
		    } //End Loop
			
			\Cache::forget('base_currency'.session('company_id'));
			\Cache::forget('currency_position'.session('company_id'));
			
			if(! $request->ajax()){
			   return redirect('company/general_settings')->with('success', _lang('Saved Sucessfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved Sucessfully')]);
			}

		}
	}

	public function crm_settings(Request $request)
    {
    	$company_id = company_id();	
    	$data = array();
    	$data['leadstatuss'] = \App\LeadStatus::where('company_id',$company_id)
    	                                      ->orderBy("order","asc")
    	                                      ->get();
											  
		$data['leadsources'] = \App\LeadSource::where('company_id',$company_id)
    	                                      ->orderBy("id","desc")
    	                                      ->get();
											  
		$data['task_statuss'] = \App\TaskStatus::where('company_id',$company_id)
    	                                       ->orderBy("order","asc")
    	                                       ->get();

        return view('backend.accounting.general_settings.crm_settings',$data);
        
	}
	
	
	public function upload_logo(Request $request){

		$this->validate($request, [
			'logo' => 'required|image|mimes:jpeg,png,jpg|max:8192',
		]);

		$company_id = company_id();

		if ($request->hasFile('logo')) {
			$image = $request->file('logo');
			$name = 'company_logo'.time().'.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/company');
			$image->move($destinationPath, $name);

			$data = array();
			$data['value'] = $name; 
			$data['company_id'] = $company_id; 
			$data['updated_at'] = Carbon::now();
			
			if(CompanySetting::where('name', "company_logo")->where("company_id",$company_id)->exists()){				
				CompanySetting::where('name','=',"company_logo")
							  ->where("company_id",$company_id)
							  ->update($data);			
			}else{
				$data['name'] = "company_logo"; 
				$data['created_at'] = Carbon::now();
				CompanySetting::insert($data); 
			}
			
			if(! $request->ajax()){
			   return redirect('company/general_settings')->with('success', _lang('Saved Sucessfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Logo Upload successfully')]);
			}

		}
	}
	
	public function upload_file($file_name,Request $request){

		if ($request->hasFile($file_name)) {
			$file = $request->file($file_name);
			$name = 'file_'.time().".".$file->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/media');
			$file->move($destinationPath, $name);

			$data = array();
			$data['value'] = $name; 
			$data['company_id'] = company_id(); 
			$data['updated_at'] = Carbon::now();
			
			if(Setting::where('name', $file_name)->exists()){				
				Setting::where('name','=',$file_name)->update($data);			
			}else{
				$data['name'] = $file_name; 
				$data['created_at'] = Carbon::now();
				Setting::insert($data); 
			}	
		}
	}
	
	
}