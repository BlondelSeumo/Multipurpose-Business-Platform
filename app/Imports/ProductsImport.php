<?php

namespace App\Imports;

use App\Product;
use App\Item;
use App\Stock;
use App\Tax;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Validator;

class ProductsImport implements ToCollection, WithStartRow
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
	            "$i.2" => 'required',
	            "$i.3" => 'required',
	            "$i.4" => 'required',
	            "$i.5" => 'required|in:inclusive,exclusive',
	        ],[
			    "$i.0.required" => _lang('Row No')." $j - "._lang('Product Name field must required'),
			    "$i.1.required" => _lang('Row No')." $j - "._lang('Product Cost field must required'),
			    "$i.2.required" => _lang('Row No')." $j - "._lang('Product Price field must required'),
			    "$i.3.required" => _lang('Row No')." $j - "._lang('Product Unit field must required'),
			    "$i.4.required" => _lang('Row No')." $j - "._lang('Initial Stock field must required'),
			    "$i.5.required" => _lang('Row No')." $j - "._lang('Tax Method field must required'),
			    "$i.5.in" 		=> _lang('Row No')." $j - "._lang('Tax Method must be inclusive or exclusive'),
			])->validate();

			$i++;
			$j++;

			//Create Item
			$item = Item::create([
				'item_name'   => $row[0],
				'item_type'   => 'product',
				'company_id'  => $company_id,
			]);

			$tax = Tax::where('tax_name',$row[5])->first();
	        
			//Create Product
			$product = Product::create([
				'item_id'        => $item['id'],
				'product_cost'   => $row[1],
				'product_price'  => $row[2],
				'product_unit'   => $row[3],
				'tax_method'     => $row[5],
				'tax_id'         => $tax ? $tax->id : '',
				'description'    => $row[7],
			
			]);

			//Create Stock Row
			Stock::create([
				'product_id'  => $item->id,
				'quantity'    => $row[4],
				'company_id'  => $company_id,
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
