<?php

namespace App\Imports;

use App\Service;
use App\Item;
use App\Tax;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;

class ServicesImport implements ToCollection, WithStartRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {   

		$company_id = company_id();
        $i = 0;
        $j = 1;

        foreach ($rows as $row) 
        {
        	if($row->filter()->isEmpty()){
	            continue;
	    	}

	    	Validator::make($rows->toArray(), [
	            "$i.0" => 'required',
	            "$i.1" => 'required',
	            "$i.2" => 'required|in:inclusive,exclusive',
	        ],[
			    "$i.0.required" => _lang('Row No')." $j - "._lang('Service Name field must required'),
			    "$i.1.required" => _lang('Row No')." $j - "._lang('Cost field must required'),
			    "$i.2.required" => _lang('Row No')." $j - "._lang('Tax Method field must required'),
			    "$i.2.in" 		=> _lang('Row No')." $j - "._lang('Tax Method must be inclusive or exclusive'),
			])->validate();

			$i++;
			$j++;

			//Create Item
			$item = Item::create([
				'item_name'   => $row[0],
				'item_type'   => 'service',
				'company_id'  => $company_id,
			]);

			$tax = Tax::where('tax_name',$row[3])->first();
	        
			//Create Service
			$service = Service::create([
				'item_id'        => $item['id'],
				'cost'   		 => $row[1],
				'tax_method'     => $row[2],
				'tax_id'         => $tax ? $tax->id : '',
				'description'    => $row[4],
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
