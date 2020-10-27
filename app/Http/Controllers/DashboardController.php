<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\Invoice;
use App\Project;
use DB;
use Auth;

class DashboardController extends Controller
{
	
	public function __construct()
    {	
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$type = Auth::user()->user_type;

		if( $type  == 'admin' ){
			$data = array();
			$data['total_user'] = \App\User::where('user_type','user')
										   ->count();

			$data['paid_user'] = \App\Company::where('membership_type','member')
										     ->count();

			$data['trial_user'] = \App\Company::where('membership_type','trial')
										      ->count();						     

			$data['total_payment'] = \App\PaymentHistory::selectRaw('SUM(amount) as total')
						                                ->where('status','paid')
													    ->first()->total;

			$data['news_users'] = \App\User::where("user_type","user")
										   ->orderBy("id","desc")
			                               ->limit(5)->get();
			
			$data['recent_payment'] = \App\PaymentHistory::where('status','paid')
										                 ->limit(5)
														 ->orderBy('id','desc')
														 ->get();														 
			
			return view('backend/dashboard-'.$type,$data);
		}else if( $type  == 'client' ){
			$client_ids = Auth::user()->client->pluck('id');
            
            $data = array();
			$data['company_currency'] = array();
			$data['currency_position'] = array();
            
            foreach(Auth::user()->client as $client){
                  $data['company_currency'][$client->company_id] = get_company_field($client->company_id,'base_currency');
                  $data['currency_position'][$client->company_id] = get_company_field($client->company_id,'currency_position');
            }


			$data['invoices'] = Invoice::whereIn('client_id',$client_ids)
									   ->orderBy('id','desc')
									   ->limit(5)
									   ->get();						   

			$data['transactions'] = Transaction::whereIn('payer_payee_id',$client_ids)
											   ->orderBy('id','desc')
											   ->limit(5)
											   ->get();

			$data['recent_projects'] = Project::whereIn('client_id',$client_ids)
											  ->orderBy('id','desc')
											  ->limit(5)
											  ->get();

			//Summary Data
			$data['total_project'] = DB::table('projects')->whereIn('client_id', $client_ids)->count();

			$data['invoice_value'] = DB::table('invoices')
										->whereIn('client_id', $client_ids)
										->selectRaw('sum(grand_total) as grand_total, sum(paid) as paid')
										->first();

			$data['invoice_due_amount'] = DB::table('invoices')
											->selectRaw('sum(grand_total) as grand_total, sum(paid) as paid')
											->whereRaw("(Status = 'Unpaid' or Status = 'Partially_Paid')")
											->whereIn('client_id',$client_ids)
											->first();								


			return view('backend/dashboard-'.$type,$data);
		}else if( $type  == 'user' ){	
			$company_id = company_id();
			$data = array();
			$data['current_month_income'] = current_month_income();
			$data['current_month_expense'] = current_month_expense();
            $project_status = \App\Project::where('company_id',$company_id)
                                          ->selectRaw('status, COUNT(id) as c')
                                          ->groupBy('status')
                                          ->get();

            foreach($project_status as $status){
            	$data['project_status'][$status->status] = $status->c;
            } 

            $data['total_invoice_count'] = \App\Invoice::where('company_id',$company_id)->count();                            
            $data['unpaid_invoice_count'] = \App\Invoice::where('status','Unpaid')
            										    ->where('company_id',$company_id)->count(); 
            $data['canceled_invoice_count'] = \App\Invoice::where('status','Canceled')
            										      ->where('company_id',$company_id)->count();   

            $data['invoice_due_amount'] = DB::table('invoices')
											->selectRaw('sum(grand_total) as grand_total, sum(paid) as paid')
											->where('company_id',$company_id)
											->whereRaw("(Status = 'Unpaid' or Status = 'Partially_Paid')")
											->first();	                        


			return view('backend/dashboard-'.$type,$data);
		}else if($type  == 'staff' ){
			$company_id = company_id();
			$data = array();

            $project_status = \App\Project::join('project_members','projects.id','project_members.project_id')
                                          ->where('project_members.user_id', Auth::id())
                                          ->selectRaw('status, COUNT(projects.id) as c')
                                          ->groupBy('status')
                                          ->get();

            $data['total_project'] = 0;
            foreach($project_status as $status){
            	$data['total_project'] += $status->c;
            	$data['project_status'][$status->status] = $status->c;
            } 
         

			return view('backend/dashboard-'.$type,$data);
		}
    }

	
	public function json_month_wise_income_expense(){
	  $income = $this->month_wise_income();
	  $expense = $this->month_wise_expense();

	  $months = '"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"';
	  $income_string = '';
	  $expense_string = '';
	  
	  foreach($income as $i){
		 $income_string = $income_string.$i->amount.",";
	  }

	  $income_string = rtrim($income_string, ",");
	  
	  foreach($expense as $e){
		 $expense_string = $expense_string.$e->amount.","; 
	  }  
	  $expense_string = rtrim($expense_string, ",");
	   
	  echo '{"Months":['.$months.'], "Income":['.$income_string.'], "Expense":['.$expense_string.']}';	  
	  exit();
	}
	
	public function json_income_vs_expense(){
	   $income = $this->current_month_income();
	   $expense = $this->current_month_expense();
	   echo '{"Income":['.$income.'], "Expense":['.$expense.']}'; 
	   exit();
	}
	
	private function month_wise_income(){
		$company_id = company_id();
		$date = date("Y-m-d");
		$query = DB::select("SELECT m.month, IFNULL(SUM(transactions.base_amount),0) as amount 
		FROM ( SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION SELECT 4 AS MONTH 
		UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH UNION SELECT 7 AS MONTH UNION SELECT 8 AS MONTH 
		UNION SELECT 9 AS MONTH UNION SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH ) AS m 
		LEFT JOIN transactions ON m.month = MONTH(trans_date) AND YEAR(transactions.trans_date)=YEAR('$date') 
		AND dr_cr='cr' AND company_id='$company_id' GROUP BY m.month ORDER BY m.month ASC");
	    return $query;
	}
	
	private function month_wise_expense(){
		$company_id = company_id();
		$date = date("Y-m-d");
		$query = DB::select("SELECT m.month, IFNULL(SUM(transactions.base_amount),0) as amount 
		FROM ( SELECT 1 AS MONTH UNION SELECT 2 AS MONTH UNION SELECT 3 AS MONTH UNION SELECT 4 AS MONTH 
		UNION SELECT 5 AS MONTH UNION SELECT 6 AS MONTH UNION SELECT 7 AS MONTH UNION SELECT 8 AS MONTH 
		UNION SELECT 9 AS MONTH UNION SELECT 10 AS MONTH UNION SELECT 11 AS MONTH UNION SELECT 12 AS MONTH ) AS m 
		LEFT JOIN transactions ON m.month = MONTH(trans_date) AND YEAR(transactions.trans_date)=YEAR('$date') 
		AND dr_cr='dr' AND company_id='$company_id' GROUP BY m.month ORDER BY m.month ASC");
	    return $query;
	}
	

  private function current_month_income(){
     $company_id = company_id();
	 $date = date("Y-m-d");
	 $query = DB::select("SELECT IFNULL(SUM(base_amount),0) as amount FROM transactions WHERE dr_cr='cr' 
	 AND trans_date BETWEEN ADDDATE(LAST_DAY(SUBDATE('$date', INTERVAL 1 MONTH)), 1) AND LAST_DAY('$date') 
	 AND company_id='$company_id'"); 
	 return $query[0]->amount;
  }
  

  private function current_month_expense(){
	 $company_id = company_id();
	 $date = date("Y-m-d");
	 $query =  DB::select("SELECT IFNULL(SUM(base_amount),0) as amount FROM transactions WHERE dr_cr='dr' 
	 AND trans_date BETWEEN ADDDATE(LAST_DAY(SUBDATE('$date', INTERVAL 1 MONTH)), 1) AND LAST_DAY('$date') 
	 AND company_id='$company_id'");
	 return $query[0]->amount;
  }

	
}
