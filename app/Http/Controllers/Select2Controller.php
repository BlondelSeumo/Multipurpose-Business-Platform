<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


class Select2Controller extends Controller
{

	public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_table_data(Request $request)
    {
		$data_where = array(
		   '1' => array('company_id'=> company_id()), //general company Data
		   '2' => array('company_id'=> company_id(), 'item_type'=> 'product'), //Item Type Product
		   '3' => array('company_id'=> company_id(), 'type'=> 'income'), //Income Category
		   '4' => array('company_id'=> company_id(), 'type'=> 'expense'), //Expense Category
		   '5' => array('company_id'=> company_id(), 'item_type'=> 'service'), //Item Type Service
		);
	
		
        $table = $request->get('table');
        $value = $request->get('value');
        $display = $request->get('display');
        $display2 = $request->get('display2');
        $where = $request->get('where');
		
        $q = $request->get('q');
	
	    $display_option = "$display as text";
		if($display2 != ''){
			$display_option = "CONCAT($display,' - ',$display2) AS text";
		}
		
	   
	    if($where != ''){
			$result = DB::table($table)
						  ->select("$value as id", DB::raw($display_option))
						  ->where($display,'LIKE',"$q%")
						  ->where($data_where[$where])
						  ->get();
		}else{
			$result = DB::table($table)
						  ->select("$value as id", DB::raw($display_option))
						  ->where($display,'LIKE',"$q%")
						  ->get();
		}			  
					  
		return $result;   
    }
	  
}
