<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;
use App\Package;
use App\PaymentHistory;
use App\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use App\Mail\PremiumMembershipMail;
use App\Utilities\Overrider;
use Validator;
use DB;
use Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
	
	public function __construct()
    {	
		date_default_timezone_set(get_option('timezone','Asia/Dhaka'));	
    }
   
    public function payment_history()
    {	
		$payment_history = \App\PaymentHistory::where("status","pending")
											  ->where('created_at', '<=', Carbon::now()->subHours(2)->toDateTimeString());
		$payment_history->delete();
		
		$payment_history = \App\PaymentHistory::where("status","paid")
											  ->orderBy('id','desc')
											  ->paginate(15);
		return view('backend.user.payments',compact('payment_history'));
    }

    public function create_offline_payment(){
       return view('backend.offline_payment.create');
    }

    public function store_offline_payment(Request $request){

    	$validator = Validator::make($request->all(), [
			'package' => 'required',
			'package_type' => 'required',
			'user' => 'required',
		]);
		
		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();				
		}

		DB::beginTransaction();
		

		$package = Package::find($request->package);
		$user = User::find($request->user);
		$company = Company::find($user->company_id);

		if($request->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
			$company->package_type = 'monthly';
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
			$company->package_type = 'yearly';
		}

		$company->membership_type = 'member';
		$company->last_email = NULL;

		 //Update Package Details
        $package = $company->package;
        $company->staff_limit = unserialize($package->staff_limit)[$company->package_type];
        $company->contacts_limit = unserialize($package->contacts_limit)[$company->package_type];
        $company->invoice_limit = unserialize($package->invoice_limit)[$company->package_type];
        $company->quotation_limit = unserialize($package->quotation_limit)[$company->package_type];
        $company->project_management_module = unserialize($package->project_management_module)[$company->package_type];
		$company->recurring_transaction = unserialize($package->recurring_transaction)[$company->package_type];
        $company->live_chat = unserialize($package->live_chat)[$company->package_type];
        $company->file_manager = unserialize($package->file_manager)[$company->package_type];
        $company->online_payment = unserialize($package->online_payment)[$company->package_type];
		$company->inventory_module = unserialize($package->inventory_module)[$company->package_type];

		$company->save();

		//Create Payment History
		$payment = new PaymentHistory();
		$payment->company_id = $company->id;
		$payment->title = "Buy {$package->package_name} Package";
		$payment->method = "Offline";
		$payment->currency = get_option('currency','USD');
		
		if($request->package_type == 'monthly'){
		    $payment->amount = $package->cost_per_month;
		}else{
		    $payment->amount = $package->cost_per_year;
		}
		$payment->package_id = $package->id;
		$payment->package_type = $request->package_type;
		$payment->status = 'paid';
		$payment->save();

		DB::commit();

		//Replace paremeter
		$replace = array(
			'{name}' => $user->name,
			'{email}' => $user->email,
			'{valid_to}' => date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace, $template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

        if($payment->id >0){
			return back()->with('success', _lang('Offline Payment Made Sucessfully'));
		}else{
			return back()->with('error', _lang('Error Occured, Please try again !'));
		}
    	
    }
	
}
