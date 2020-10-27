<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
	
	public function __construct()
    {
		date_default_timezone_set(get_company_option('timezone',get_option('timezone','Asia/Dhaka')));	
	}
	
    public function account_statement(Request $request, $view=""){
		
		if($view == ''){
		   return view('backend.accounting.reports.account_statement');	
		}else if($view == "view"){
		   $data = array();
		   $dr_cr = $request->trans_type;
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $account = $request->account;
		   $company_id = company_id();
		   
		   if($dr_cr == "dr"){
			 $data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,'' as debit,opening_balance as credit FROM accounts WHERE id='$account' 
			 UNION ALL
			 SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='dr' AND trans_date<'$date1' AND account_id='$account') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND trans_date<'$date1' AND account_id='$account') as credit
			 UNION ALL 
			 SELECT trans_date,note,IF(dr_cr='dr',amount,NULL) as debit,IF(dr_cr='cr',amount,NULL) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account' AND dr_cr='dr'"); 
		   
		   }else if($dr_cr == "cr"){
				$data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,'' as debit,opening_balance as credit FROM accounts WHERE id='$account' 
				UNION ALL
				SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='dr' AND trans_date<'$date1' AND account_id='$account') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND trans_date<'$date1' AND account_id='$account') as credit
				UNION ALL 
				SELECT trans_date,note,IF(dr_cr='dr',amount,NULL) as debit,IF(dr_cr='cr',amount,NULL) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account' AND dr_cr='cr'"); 
			   
		   }else if($dr_cr == "all"){
				$data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,0 as debit,opening_balance as credit FROM accounts WHERE id='$account' 
				UNION ALL
				SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='dr' AND trans_date<'$date1' AND account_id='$account') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND date<'$date1' AND account_id='$account') as credit
				UNION ALL 
				SELECT trans_date,note,IF(dr_cr='dr',amount,NULL) as debit,IF(dr_cr='cr',amount,NULL) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account'");   			   
		   }
		   
		   $data['dr_cr'] = $request->trans_type;
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   $data['account'] = $request->account;
		   $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
		   
		   return view('backend.accounting.reports.account_statement',$data);
		 }
    }
	
	//Day Wise Income Report
	public function day_wise_income(Request $request,$view = ""){
		if($view == ''){
			return view('backend.accounting.reports.day_wise_income_report');	
		}else{
			$date1 = $request->date1;
			$date2 = $request->date2;
			$account = $request->account;
			$company_id = company_id();
			$data = array();
			
			$data['report_data'] = DB::select("SELECT transactions.trans_date,chart_of_accounts.name as income_type,transactions.note,accounts.account_title as account,transactions.amount 
			FROM transactions JOIN accounts ON transactions.account_id = accounts.id LEFT JOIN chart_of_accounts ON transactions.chart_id=chart_of_accounts.id
			WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND accounts.id='$account' AND transactions.company_id='$company_id'
			UNION ALL 
			SELECT '$date2','Total Amount','','',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id = accounts.id AND transactions.trans_date 
			BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND accounts.id='$account' AND transactions.company_id='$company_id'");
			
			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['account'] = $request->account;
		    $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
			return view('backend.accounting.reports.day_wise_income_report',$data);
		}
	
	}
	
	
	//Date Wise Income Report
	public function date_wise_income(Request $request, $view = ""){
		if($view == ''){
		   return view('backend.accounting.reports.date_wise_income_report');	
		}else{
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $account = $request->account;
		   $company_id = company_id();
		   $data = array();
		   
		   $data['report_data'] = DB::select("SELECT DATE_FORMAT(transactions.trans_date,'%d %b, %Y') as trans_date,SUM(transactions.amount) as amount 
		   FROM transactions WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND transactions.account_id='$account' AND transactions.company_id='$company_id' GROUP BY transactions.trans_date
		   UNION ALL 
		   SELECT '$date2',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id=accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND transactions.company_id='$company_id'");
		   
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   $data['account'] = $request->account;
		   $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
		   return view('backend.accounting.reports.date_wise_income_report',$data);
		}
	}
	
	
	//Day Wise Expense Report
	public function day_wise_expense(Request $request,$view = ""){
		if($view == ''){
			return view('backend.accounting.reports.day_wise_expense_report');	
		}else{
			$date1 = $request->date1;
			$date2 = $request->date2;
			$account = $request->account;
			$company_id = company_id();
			$data = array();
			
			$data['report_data'] = DB::select("SELECT transactions.trans_date,chart_of_accounts.name as expense_type,transactions.note,accounts.account_title as account,transactions.amount 
			FROM transactions JOIN accounts ON transactions.account_id = accounts.id LEFT JOIN chart_of_accounts ON transactions.chart_id=chart_of_accounts.id
			WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND accounts.id='$account' AND transactions.dr_cr='dr' AND transactions.company_id='$company_id'
			UNION ALL 
			SELECT '$date2','Total Amount','','',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id = accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND accounts.id='$account' AND transactions.dr_cr='dr' AND transactions.company_id='$company_id'");
			
			$data['date1'] = $request->date1;
			$data['date2'] = $request->date2;
			$data['account'] = $request->account;
		    $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
			return view('backend.accounting.reports.day_wise_expense_report',$data);
		}
	
	}
	
	
	//Date Wise Expense Report
	public function date_wise_expense(Request $request, $view = ""){
		if($view == ''){
		   return view('backend.accounting.reports.date_wise_expense_report');	
		}else{
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $account = $request->account;
		   $company_id = company_id();
		   $data = array();
		   
		   $data['report_data'] = DB::select("SELECT DATE_FORMAT(transactions.trans_date,'%d %b, %Y') as trans_date,SUM(transactions.amount) as amount 
		   FROM transactions WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.account_id='$account' AND transactions.dr_cr='dr' AND transactions.company_id='$company_id' GROUP BY transactions.trans_date
		   UNION ALL 
		   SELECT '$date2',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id=accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.account_id='$account' AND transactions.dr_cr='dr' AND transactions.company_id='$company_id'");
		   
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   $data['account'] = $request->account;
		   $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
		   return view('backend.accounting.reports.date_wise_expense_report',$data);
		}
	}
	
	public function transfer_report(Request $request, $view = ""){
		if($view == ''){
		   return view('backend.accounting.reports.transfer_report');	
		}else{
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $company_id = company_id();
		   $data = array();
		   $data['report_data'] = DB::select("SELECT transactions.trans_date,transactions.note,accounts.account_title as account,accounts.account_currency, dr_cr,
		   IF(transactions.dr_cr='dr',transactions.amount,NULL) as debit,IF(transactions.dr_cr='cr',transactions.amount,NULL) as credit 
		   FROM transactions,accounts WHERE transactions.account_id=accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' 
		   AND transactions.type='transfer' AND transactions.company_id='$company_id'");
		   
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   return view('backend.accounting.reports.transfer_report',$data);
		}
	}
	
	//Income Vs Expense Report
     public function income_vs_expense(Request $request,$view = ''){
        if ($view == '') {
            return view('backend.accounting.reports.income_vs_expense_report');
        }else if ($view == 'view') {
            $date1 = $request->date1;
		    $date2 = $request->date2;
		    $account = $request->account;
			$company_id = company_id();
            
			$data['report_data'] = $this->get_income_vs_expense($date1, $date2, $account);	
			
			$data['date1'] = $request->date1;
		    $data['date2'] = $request->date2;
			$data['account'] = $request->account;
		    $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
			return view('backend.accounting.reports.income_vs_expense_report',$data);
		}
    }
	
	
	//Report By Payer
	public function report_by_payer(Request $request, $view=""){
		if($view == ''){
		   return view('backend.accounting.reports.report_by_payer');	
		}else{
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $account = $request->account;
		   $payer_id = $request->payer_id;
		   $company_id = company_id();
		   $data = array();
		   
		   $data['report_data'] = DB::select("SELECT trans_date, chart_of_accounts.name as c_type,transactions.note,accounts.account_title as account,transactions.amount,contacts.contact_name as payer
		   FROM transactions,accounts,contacts,chart_of_accounts WHERE transactions.account_id=accounts.id AND transactions.payer_payee_id=contacts.id 
		   AND transactions.chart_id=chart_of_accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND accounts.id='$account' AND transactions.dr_cr='cr' AND transactions.payer_payee_id='$payer_id' AND transactions.company_id='$company_id'
		   UNION ALL
		   SELECT '$date2','','TOTAL AMOUNT','',SUM(transactions.amount) as amount,'' FROM transactions 
		   WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.account_id='$account' AND transactions.dr_cr='cr' AND transactions.payer_payee_id='$payer_id' AND transactions.company_id='$company_id'");
		   
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   $data['payer_id'] = $request->payer_id;
		   $data['account'] = $request->account;
		   $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
		   return view('backend.accounting.reports.report_by_payer',$data);
		}
	}
	
	//Report By Payee
	public function report_by_payee(Request $request, $view=""){
		if($view == ''){
		   return view('backend.accounting.reports.report_by_payee');	
		}else{
		   $date1 = $request->date1;
		   $date2 = $request->date2;
		   $account = $request->account;
		   $payee_id = $request->payee_id;
		   $company_id = company_id();
		   $data = array();
		   
		   $data['report_data'] = DB::select("SELECT trans_date,chart_of_accounts.name as c_type,transactions.note,accounts.account_title as account,transactions.amount,contacts.contact_name as payee
		   FROM transactions,accounts,contacts,chart_of_accounts WHERE transactions.account_id=accounts.id AND transactions.payer_payee_id=contacts.id 
		   AND transactions.chart_id=chart_of_accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND accounts.id='$account' AND transactions.dr_cr='dr' AND transactions.payer_payee_id='$payee_id' AND transactions.company_id='$company_id'
		   UNION ALL
		   SELECT '$date2','','TOTAL AMOUNT','',SUM(transactions.amount) as amount,'' FROM transactions 
		   WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.account_id='$account' AND transactions.dr_cr='dr' AND transactions.payer_payee_id='$payee_id' AND transactions.company_id='$company_id'");
		   
		   $data['date1'] = $request->date1;
		   $data['date2'] = $request->date2;
		   $data['payee_id'] = $request->payee_id;
		   $data['account'] = $request->account;
		   $data['acc'] = DB::table('accounts')	   
							 ->where('id',$request->account)
							 ->where('company_id',$company_id)
							 ->first();
		   return view('backend.accounting.reports.report_by_payee',$data);
		}
	}
	
	
	//private methods
	private function get_income_vs_expense($from_date, $to_date, $account){
		$company_id = company_id();
		
		$income = DB::select("SELECT id FROM transactions 
				  WHERE dr_cr='cr' AND company_id='$company_id' AND trans_date between '" . $from_date . "'
				  AND '" . $to_date . "' AND transactions.account_id='$account'");
				  
		$expense = DB::select("SELECT id FROM transactions 
				  WHERE dr_cr='dr' AND company_id='$company_id' AND trans_date between '" . $from_date . "'
				  AND '" . $to_date . "' AND transactions.account_id='$account'");
		
		if(count($income) > count($expense)){
			return DB::select("SELECT income.*,expense.* FROM (SELECT @a:=@a+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') income_date,transactions.note as income_note,chart_of_accounts.name as income_type,transactions.amount income_amount 
			    FROM transactions,accounts,chart_of_accounts, (SELECT @a:= 0) AS a WHERE 
				transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='cr' 
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date' AND transactions.account_id='$account') as income LEFT JOIN 
				(SELECT @b:=@b+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') expense_date,transactions.note as expense_note,chart_of_accounts.name as expense_type,transactions.amount expense_amount FROM transactions,accounts,chart_of_accounts, 
				(SELECT @b:= 0) AS a WHERE transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='dr' 
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date' AND transactions.account_id='$account') as expense ON income.sl=expense.sl");
		}else{
			return DB::select("SELECT income.*,expense.* FROM (SELECT @a:=@a+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') income_date,transactions.note as income_note,chart_of_accounts.name as income_type,transactions.amount income_amount  
			    FROM transactions,accounts,chart_of_accounts, (SELECT @a:= 0) AS a WHERE 
				transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='cr' 
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date' AND transactions.account_id='$account') as income RIGHT JOIN 
				(SELECT @b:=@b+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') expense_date,transactions.note as expense_note,chart_of_accounts.name as expense_type,transactions.amount expense_amount FROM transactions,accounts,chart_of_accounts, 
				(SELECT @b:= 0) AS a WHERE transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='dr' 
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date' AND transactions.account_id='$account') as expense ON income.sl=expense.sl");
		}	
	}
		
}