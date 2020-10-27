<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Stripe\Stripe;
use Stripe\Charge;
use Razorpay\Api\Api;
use App\PaymentHistory;
use App\EmailTemplate;
use App\Package;
use App\Company;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PremiumMembershipMail;
use App\Utilities\Overrider;
use Auth;

class MembershipController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    	date_default_timezone_set(get_option('timezone','Asia/Dhaka'));	
    }

	/**
	* Show the membership extend form.
	*
	* @return \Illuminate\Http\Response
	*/
    public function my_subscription()
    {
    	$user = Auth::user();
		return view('membership.subscription_details', compact('user'));
    }

   /**
	* Show the membership extend form.
	*
	* @return \Illuminate\Http\Response
	*/
    public function extend()
    {
    	$user = Auth::user();
		return view('membership.extend', compact('user'));
    }
	
	public function pay(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'package' => 'required',
			'package_type' => 'required',
			'gateway' => 'required',
		]);
		
		if ($validator->fails()) {
			return redirect('membership/extend')->withErrors($validator)
												->withInput();				
		}
		
		
		$data = array();

		$package = Package::find($request->package);

		$data['title'] = "Buy {$package->package_name} Package";

		if($request->package_type == 'monthly'){
			$data['amount'] = $package->cost_per_month;
		    $data['custom'] = $request->package_type;
		}else{
			$data['amount'] = $package->cost_per_year;
		    $data['custom'] = $request->package_type;
		}

		
		//Create Pending Payment
		$payment = new PaymentHistory();
		$payment->company_id = company_id();
		$payment->title = $data['title'];
		$payment->method = "";
		$payment->currency = get_option('currency','USD');
		$payment->amount = $data['amount'];
		$payment->package_id = $package->id;
		$payment->package_type = $request->package_type;
		$payment->status = 'pending';
		$payment->save();
		
		$data['payment_id'] = $payment->id;
		
		if($request->gateway == "PayPal"){
			return view('membership.gateway.paypal',$data);
		}elseif($request->gateway == "Stripe"){

			Stripe::setApiKey(get_option('stripe_secret_key'));

			$session = \Stripe\Checkout\Session::create([
			  'payment_method_types' => ['card'],
			  'line_items' => [[
			    'price_data' => [
			      'product_data' => [
			          'name' => $data['title'],
			          'description' => $data['title'],
			       ],
			      'unit_amount' =>   round(convert_currency(get_option('currency','USD'), get_option('stripe_currency','USD'), ($data['amount'] * 100))),
			      'currency' 	=>   get_option('stripe_currency','USD'),
			    ],
			    'quantity' => 1,
			  ]],
			  'mode' => 'payment',
			  'success_url' => url('membership/stripe_payment/success/'.$payment->id),
			  'cancel_url' => url('membership/stripe_payment/cancel'),
			]);

			$data['session_id'] = $session->id;
			session(['session_id' => $session->id]);

			return view('membership.gateway.stripe',$data);
		}elseif($request->gateway == "Razorpay"){
			$api = new Api(get_option('razorpay_key_id'), get_option('razorpay_secret_key'));

			$orderData = [
			    'receipt'         => $payment->id,
			    'amount'          => round(convert_currency(get_option('currency','USD'), 'INR',($data['amount'] * 100))),
			    'currency'        => 'INR',
			    'payment_capture' => 1 // auto capture
			];

			$razorpayOrder = $api->order->create($orderData);
			$razorpayOrderId = $razorpayOrder['id'];
			session(['razorpay_order_id' => $razorpayOrderId]);
			$data['amount'] = $orderData['amount'];
			$data['order_id'] = $razorpayOrderId;

			return view('membership.gateway.razorpay', $data);
		}else if($request->gateway == "Paystack"){
			return view('membership.gateway.paystack', $data);
		}
    }
	
	//PayPal Payment Gateway
	public function paypal($action){
		if($action == "return"){
			return redirect('/dashboard')->with('paypal_success', _lang('Thank you, You have sucessfully extended your membership. Please wait until you get confrimation email if you still see your membership has expired.'));
		}else if($action == "cancel"){
			return redirect('membership/extend')->with('message', _lang('Payment Canceled !'));
		}
	}
	

	public function paypal_ipn(Request $request)
	{
		$payment_id = $request->item_number;
		//$amount = $request->mc_gross;
		$amount = convert_currency(get_option('paypal_currency','USD'), get_option('currency','USD'), $request->mc_gross);
		 
		$payment = PaymentHistory::find($payment_id);
		//$increment = $payment->extend;
		
		if( $amount >= $payment->amount){

			$company = Company::find($payment->company_id);

			if($payment->package_type == 'monthly'){
				$company->valid_to = date('Y-m-d', strtotime('+1 months'));
			}else{
				$company->valid_to = date('Y-m-d', strtotime('+1 year'));
			}

			$company->membership_type = 'member';
			$company->last_email = NULL;
			$company->package_id = $payment->package_id;

			 //Update Package Details
	        $package = $payment->package;
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

			//Save payment History
			$payment->method = "PayPal";
			$payment->status = 'paid';
			$payment->save();
			
			
			//Replace paremeter
			$user = User::where('company_id',$company->id)
						->where('user_type','user')
						->first();

			$replace = array(
				'{name}'=>$user->name,
				'{email}'=>$user->email,
				'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
			);
			
			//Send email Confrimation
			Overrider::load("Settings");
			$template = EmailTemplate::where('name','premium_membership')->first();
			$template->body = process_string($replace,$template->body);

			try{
				Mail::to($user->email)->send(new PremiumMembershipMail($template));
			}catch (\Exception $e) {
				//Nothing
			}
			
        }		
    }
	
	//Stripe payment Gateway
	public function stripe_payment($action, $payment_id){

		if($action == 'cancel'){
			return redirect('/dashboard')->with('error', _lang('Payment Cancelled !'));
		}

		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		Stripe::setApiKey(get_option('stripe_secret_key'));

		$session = \Stripe\Checkout\Session::retrieve(session('session_id'));
		

        $payment = PaymentHistory::find($payment_id);

        if($session->amount_total != round(convert_currency(get_option('currency','USD'), get_option('stripe_currency','USD'), ($payment->amount * 100)))){
			return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
		}
		
		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
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
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "Stripe";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

		//Forget Session
		request()->session()->forget('session_id');

        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
	}


	//Razorpay payment Gateway
	public function razorpay_payment($payment_id){
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);
		
		$api = new Api(get_option('razorpay_key_id'), get_option('razorpay_secret_key'));

	    try{
	        $attributes = array(
	            'razorpay_order_id' 	=> session('razorpay_order_id'),
	            'razorpay_payment_id' 	=> $_POST['razorpay_payment_id'],
	            'razorpay_signature' 	=> $_POST['razorpay_signature']
	        );

	        $api->utility->verifyPaymentSignature($attributes);

	        $charge = $api->payment->fetch($_POST['razorpay_payment_id']);

	        $payment = PaymentHistory::find($payment_id);

	        if($charge->amount != round(convert_currency(get_option('currency','USD'), 'INR',($payment->amount * 100)))){
				return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
			}
		
			$company = Company::find($payment->company_id);

			if($payment->package_type == 'monthly'){
				$company->valid_to = date('Y-m-d', strtotime('+1 months'));
			}else{
				$company->valid_to = date('Y-m-d', strtotime('+1 year'));
			}

			$company->membership_type = 'member';
			$company->last_email = NULL;
			$company->package_id = $payment->package_id;

			//Update Package Details
	        $package = $payment->package;
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
			
			session(['valid_to' => $company->valid_to]);

			//Save payment History
			$payment->method = "Rezorpay";
			$payment->status = 'paid';
			$payment->save();
			

			//Replace paremeter
	        $user = User::where('company_id',$company->id)
	                    ->where('user_type','user')
	                    ->first();
			$replace = array(
				'{name}' =>$user->name,
				'{email}' =>$user->email,
				'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
			);
			
			//Send email Confrimation
			Overrider::load("Settings");
			$template = EmailTemplate::where('name','premium_membership')->first();
			$template->body = process_string($replace,$template->body);

			try{
				Mail::to($user->email)->send(new PremiumMembershipMail($template));
			}catch (\Exception $e) {
				//Nothing
			}

			//Forget Session
			request()->session()->forget('razorpay_order_id');

	        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));

	    }catch(SignatureVerificationError $e){
	        $success = false;
	        $error = 'Razorpay Error : ' . $e->getMessage();
	        return redirect('/dashboard')->with('error', $error);
	    }	
        
	}

	//Paystack payment Gateway
	public function paystack_payment($payment_id, $reference){
		@ini_set('max_execution_time', 0);
		@set_time_limit(0);

		$payment = PaymentHistory::find($payment_id);
		
		$curl = curl_init();
  
		 curl_setopt_array($curl, array(
		    CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => "GET",
		    CURLOPT_HTTPHEADER => array(
		      "Authorization: Bearer " . get_option('paystack_secret_key'),
		      "Cache-Control: no-cache",
		    ),
		 ));
		  
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		  
		if ($err) {
		    return redirect('/dashboard')->with('error', $err);
		} 

		$charge = json_decode($response);

		if($charge->data->amount != round(convert_currency(get_option('currency','USD'), get_option('paystack_currency','GHS'),($payment->amount * 100)))){
			return redirect('/dashboard')->with('error', _lang('illegal Operation !'));
		}
		
		
		$company = Company::find($payment->company_id);
		if($payment->package_type == 'monthly'){
			$company->valid_to = date('Y-m-d', strtotime('+1 months'));
		}else{
			$company->valid_to = date('Y-m-d', strtotime('+1 year'));
		}
		$company->membership_type = 'member';
		$company->last_email = NULL;
		$company->package_id = $payment->package_id;

		//Update Package Details
        $package = $payment->package;
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
		
		session(['valid_to' => $company->valid_to]);

		//Save payment History
		$payment->method = "PayStack";
		$payment->status = 'paid';
		$payment->save();
		

		//Replace paremeter
        $user = User::where('company_id',$company->id)
                    ->where('user_type','user')
                    ->first();
		$replace = array(
			'{name}' =>$user->name,
			'{email}' =>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($company->valid_to)),
		);
		
		//Send email Confrimation
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','premium_membership')->first();
		$template->body = process_string($replace,$template->body);

		try{
			Mail::to($user->email)->send(new PremiumMembershipMail($template));
		}catch (\Exception $e) {
			//Nothing
		}

        return redirect('/dashboard')->with('success', _lang('Thank you, You have sucessfully extended your membership.'));
	}
	
}
