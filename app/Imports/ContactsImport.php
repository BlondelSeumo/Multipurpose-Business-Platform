<?php

namespace App\Imports;

use App\Contact;
use App\User;
use App\ContactGroup;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;

class ContactsImport implements ToCollection, WithStartRow
{
	
	private $data; 

    public function __construct(array $data = [])
    {
        $this->data = $data; 
    }
	
	/**
    * @param Collection $rows
    */
	public function collection(Collection $rows)
    {
		$row_count = count($rows);
		if( ! available_limit( 'contacts_limit', $row_count ) ){
			return back()->with('error', _lang('Sorry, Your contacts limit is not enough !'));  
		}
		
		$company_id = company_id();
		$i = 0;
        $j = 1;
	
        foreach ($rows as $row) 
        {
        	if($row->filter()->isEmpty()){
	            continue;
	    	}

			Validator::make($rows->toArray(), [
	            "$i.0" => 'required|in:Company,Individual',
	            "$i.2" => 'required',
	            "$i.3" => 'required',
	            "$i.8" => 'required|max:3',
	        ],[
			    "$i.0.required" => _lang('Row No')." $j - "._lang('Profile Type field must required'),
			    "$i.0.in"       => _lang('Row No')." $j - "._lang('Profile Type must be Company or Individual'),
			    "$i.2.required" => _lang('Row No')." $j - "._lang('Contact Name field must required'),
			    "$i.3.required" => _lang('Row No')." $j - "._lang('Contact Email field must required'),
			    "$i.8.required" => _lang('Row No')." $j - "._lang('Currency field must required'),
			    "$i.8.max" 		=> _lang('Row No')." $j - "._lang('Currency field must be in 3 character'),
			])->validate();

			$i++;
			$j++;

            $user_id = null;
			$user = User::where('email',$row[3])
						  ->where('user_type','client')->first();
			if($user){
				$user_id = $user->id;
			}	

			//If Contact email already exists
			$client = Contact::where('contact_email',$row[3])->first();
			if($client){
				continue;
			}
			
			//If Contact email already assign to company or staff
			$other = User::where('email',$row[3])
						 ->where('user_type','!=','client')->first();
			if($other){
				continue;
			}

			
			Contact::create([
				'profile_type'      => $row[0],
				'company_name'      => $row[1],
				'contact_name'      => $row[2],
				'contact_email'     => $row[3],
				'vat_id'     		=> $row[4],
				'reg_no'     		=> $row[5],
				'contact_phone'     => $row[6],
				'country'     		=> $row[7],
				'currency'    		=> $row[8],
				'city'     			=> $row[9],
				'state'    			=> $row[10],
				'zip'     			=> $row[11],
				'address'     		=> $row[12],
				'facebook'     		=> $row[13],
				'twitter'     		=> $row[14],
				'linkedin'     		=> $row[15],
				'remarks'     		=> $row[16],
				'contact_image'     => 'avatar.png',
				'group_id'     		=> $this->data['group_id'],
				'user_id'     		=> $user_id,
				'company_id'     	=> $company_id,
			]);

        }
  
    }
	
	/**
     * @return int
    */
    public function startRow(): int
    {
        return 2;
    }
	
}
