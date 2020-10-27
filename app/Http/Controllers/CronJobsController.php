<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
use App\RepeatTransaction;
use App\User;
use App\EmailTemplate;
use App\Company;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertNotificationMail;
use App\Utilities\Overrider;
use DB;

class CronJobsController extends Controller
{
	
    /**
     * Show the application CronJobs.
     *
     * @return \Illuminate\Http\Response
     */
    public function run()
    {
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		/** Update Currency Exchange Rate **/
		update_currency_exchange_rate(true);
		
		/** Process Repeat Transactions **/
		$date = date("Y-m-d");
		$repeat_transaction = RepeatTransaction::where('trans_date',$date)
		                                       ->where('status',0)
		                                       ->get();
											   
		foreach($repeat_transaction as $transaction){
			if($transaction->type == 'income'){
				$trans = new Transaction();
				$trans->trans_date = $transaction->trans_date;
				$trans->account_id = $transaction->account_id;
				$trans->chart_id = $transaction->chart_id;
				$trans->type = 'income';
				$trans->dr_cr = 'cr';
				$trans->amount = $transaction->amount;
				$trans->base_amount = $transaction->base_amount;
				$trans->payer_payee_id = $transaction->payer_payee_id;
				$trans->payment_method_id = $transaction->payment_method_id;
				$trans->reference = $transaction->reference;
				$trans->note = $transaction->note;
				$trans->company_id = $transaction->company_id;
				$trans->save();
				
				$transaction->trans_id = $trans->id;
				$transaction->status = 1;
				$transaction->save();		
			}else if($transaction->type == 'expense'){
				$trans = new Transaction();
				$trans->trans_date = $transaction->trans_date;
				$trans->account_id = $transaction->account_id;
				$trans->chart_id = $transaction->chart_id;
				$trans->type = 'expense';
				$trans->dr_cr = 'dr';
				$trans->amount = $transaction->amount;
				$trans->base_amount = $transaction->base_amount;
				$trans->payer_payee_id = $transaction->payer_payee_id;
				$trans->payment_method_id = $transaction->payment_method_id;
				$trans->reference = $transaction->reference;
				$trans->note = $transaction->note;
				$trans->company_id = $transaction->company_id;
				$trans->save();
				
				$transaction->trans_id = $trans->id;
				$transaction->status = 1;
				$transaction->save();
			}
		}
		
		/** Send Alert Notification to User before expiry package **/
		$days_before = 14;
		$user_list = DB::select("SELECT users.*, companies.valid_to FROM users JOIN companies ON users.company_id = companies.id WHERE DATEDIFF(companies.valid_to, CURDATE()) <= $days_before AND companies.last_email IS NULL AND users.user_type='user'");
        
		if (count($user_list) > 0) {
            foreach ($user_list as $user) {
				/** Replace Paremeter **/
				$replace = array(
					'{name}'     => $user->name,
					'{email}'    => $user->email,
					'{valid_to}' => date('d M, Y', strtotime($user->valid_to)),
				);
				
				//Send email Confrimation
				Overrider::load("Settings");
				$template = EmailTemplate::where('name','alert_notification')->first();
				$template->body = process_string($replace, $template->body);
				
				try{
					Mail::to($user->email)->send(new AlertNotificationMail($template));
				}catch (\Exception $e) {
					//Noting
				}	
                $company = Company::find($user->company_id);
                $company->last_email = date('Y-m-d');
				$company->save();
            }

        }

        echo 'Scheduled task runs successfully';
	
    }

}
