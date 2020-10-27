<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use App\Setting;
use Carbon\Carbon;
use DB;
use App\Utilities\PHPMySQLBackup;

class UtilityController extends Controller
{
    /**
     * Show the Settings Page.
     *
     * @return Response
     */

	public function __construct(){
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		date_default_timezone_set( get_option('timezone','Asia/Dhaka') );	
	} 
	 
    public function settings($store = '',Request $request)
    {
		if($store == ''){
           return view('backend.administration.general_settings.settings');
        }else{	   
		    foreach($_POST as $key => $value){
				 if($key == "_token"){
					 continue;
				 }
				 
				 $data = array();
				 $data['value'] = $value; 
				 $data['updated_at'] = Carbon::now();
				 if(Setting::where('name', $key)->exists()){				
					Setting::where('name','=',$key)->update($data);			
				 }else{
					$data['name'] = $key; 
					$data['created_at'] = Carbon::now();
					Setting::insert($data); 
				 }
		    } //End Loop
			
			foreach($_FILES as $key => $value){
			   $this->upload_file($key,$request);
			}
			
			//Update Currency exchange Rate
			update_currency_exchange_rate();
			
			\Cache::forget('base_currency'.session('company_id'));
			\Cache::forget('currency_position'.session('company_id'));
			\Cache::forget('membership_system');
			
			if(! $request->ajax()){
			   return redirect('administration/general_settings')->with('success', _lang('Saved Sucessfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved Sucessfully')]);
			}
			//return redirect('administration/general_settings')->with('success',_lang('Saved Sucessfully'));
		}
	}
	
	
	
	public function upload_logo(Request $request){
		$this->validate($request, [
			'logo' => 'required|image|mimes:jpeg,png,jpg|max:8192',
		]);

		if ($request->hasFile('logo')) {
			$image = $request->file('logo');
			$name = 'logo.'.$image->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/media');
			$image->move($destinationPath, $name);

			$data = array();
			$data['value'] = $name; 
			$data['updated_at'] = Carbon::now();
			
			if(Setting::where('name', "logo")->exists()){				
				Setting::where('name','=',"logo")->update($data);			
			}else{
				$data['name'] = "logo"; 
				$data['created_at'] = Carbon::now();
				Setting::insert($data); 
			}
			
			if(! $request->ajax()){
			   return redirect('administration/general_settings')->with('success', _lang('Saved Sucessfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Logo Upload successfully')]);
			}

		}
	}
	
	public function upload_file($file_name, Request $request){

		if ($request->hasFile($file_name)) {
			$file = $request->file($file_name);
			if(is_array($file)){
				$file = $file[array_key_first($file)];
			}
			$name = 'file_'.time() . '.' . $file->getClientOriginalExtension();
			$destinationPath = public_path('/uploads/media');
			$file->move($destinationPath, $name);

			$data = array();
			$data['value'] = $name; 
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
	
	
	public function theme_option($store = '',Request $request)
    {		
	    if($store == ''){
			$theme = get_option('active_theme','default');
            return view("theme.$theme.theme_option.theme_option");
        }else{
			foreach($_POST as $key => $value){
				 if($key == "_token"){
					 continue;
				 }
				 
				 $data = array();
				 $data['value'] = is_array($value) ? serialize($value) : $value; 
				 $data['updated_at'] = Carbon::now();
				 if(Setting::where('name', $key)->exists()){				
					Setting::where('name','=',$key)->update($data);			
				 }else{
					$data['name'] = $key; 
					$data['created_at'] = Carbon::now();
					Setting::insert($data); 
				 }

			} //End $_POST Loop
			
			//Upload File
			foreach($_FILES as $key => $value){
			   $this->upload_file($key, $request);
			}	
	
			if(! $request->ajax()){
			   return redirect()->back()->with('success', _lang('Saved sucessfully'));
			}else{
			   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved sucessfully')]);
			}
		}
	}
	
	/** Show Exchange rates **/
	public function currency_rates(Request $request, $id = ''){
		if($id == ''){
			$currency_rates = \App\CurrencyRate::all();
			return view('backend.administration.currency_rate.list', compact('currency_rates'));
	    }else{
			$currency_rate = \App\CurrencyRate::find($id);
			
			if(! $request->isMethod('patch')){
				return view('backend.administration.currency_rate.modal.edit', compact('currency_rate'));
		    }else{
				$currency_rate->rate = $request->rate;
				$currency_rate->save();
				if(! $request->ajax()){
				   return redirect()->back()->with('success', _lang('Saved sucessfully'));
				}else{
				   return response()->json(['result'=>'success','action'=>'update','message'=>_lang('Saved sucessfully')]);
				}
			}
		}
	}
	
	
	public function backup_database(){
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
			
		$return = "";
		$database = 'Tables_in_'.DB::getDatabaseName();
		$tables = array();
		$result = DB::select("SHOW TABLES");

		foreach($result as $table){
			$tables[] = $table->$database;
		}


		//loop through the tables
		foreach($tables as $table){			
			$return .= "DROP TABLE IF EXISTS $table;";

			$result2 = DB::select("SHOW CREATE TABLE $table");
			$row2 = $result2[0]->{'Create Table'};

			$return .= "\n\n".$row2.";\n\n";
			
			$result = DB::select("SELECT * FROM $table");

			foreach($result as $row){	
				$return .= "INSERT INTO $table VALUES(";
				foreach($row as $key=>$val){	
					$return .= "'".addslashes($val)."'," ;	
				}
				$return = substr_replace($return, "", -1);
				$return .= ");\n";
			}
   
			$return .= "\n\n\n";
		}

		//save file
		$file = 'public/backup/DB-BACKUP-'.time().'.sql';
		$handle = fopen($file,'w+');
		fwrite($handle,$return);
		fclose($handle);
		
		return response()->download($file);
		return redirect()->back()->with('success', _lang('Backup Created Sucessfully'));		
	}
	
}