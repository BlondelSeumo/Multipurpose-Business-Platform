<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Company;
use App\Contact;
use App\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Utilities\Overrider;
use DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    
	//protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		Overrider::load("Settings");
        $this->middleware('guest');
    }
	
	public function redirectTo(){
		if(auth()->user()->user_type == "user"){
			if(has_membership_system() == 'enabled'){
				if( membership_validity() < date('Y-m-d')){
				    return 'membership/extend';
				}
			}
		}
		return '/dashboard';
	}
	
	public function showRegistrationForm()
	{
		if(get_option('allow_singup','yes') != 'yes'){
			return redirect('login');
		}else{
			return view('auth.register');
		}
	}

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'business_name' => ['required', 'string', 'max:191'],
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'package' => ['required'],
            'package_type' => ['required'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

		$trial_period = get_option('trial_period', 14);
		
		if($trial_period < 1){
			$valid_to = date('Y-m-d', strtotime(date('Y-m-d'). " -1 day"));
		}else{
			$valid_to = date('Y-m-d', strtotime(date('Y-m-d'). " + $trial_period days"));
		}
		
        DB::beginTransaction();
        //Create Company
        $company = new Company();
        $company->business_name = $data['business_name'];
        $company->package_id = $data['package'];
        $company->package_type = $data['package_type'];
        $company->membership_type = 'trial';
        $company->status = 1;
        $company->valid_to = $valid_to;

        //Package Details
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

         //Create User      
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
		if( get_option('email_verification') == 'disabled' ){
			$user->email_verified_at = now();
		}
        $user->password = Hash::make($data['password']);
        $user->user_type = 'user';
        $user->status = 1;
        $user->profile_picture = 'default.png';
        $user->company_id = $company->id;
        $user->save();

        DB::commit();
		
		return $user;
    }
	
	public function client_signup(Request $request){
		if($request->isMethod('get')){
			
			return view('auth.client_signup');
			
		}else if($request->isMethod('post')){
			
			$validator = Validator::make($request->all(), [
				'name' => 'required|max:191',
				'email' => 'required|email|unique:users|max:191',
				'password' => 'required|max:20|min:6|confirmed',
			]);
			
			if ($validator->fails()) {
				if($request->ajax()){ 
					return response()->json(['result'=>'error','message'=>$validator->errors()->all()]);
				}else{
					return back()->withErrors($validator)
								 ->withInput();
				}			
			}
			
			//Create User	
			DB::beginTransaction();
			
			$user = new User();
			$user->name = $request->name;
			$user->email = $request->email;
			if( get_option('email_verification') == 'disabled' ){
				$user->email_verified_at = now();
			}
			$user->password = Hash::make($request->password);
			$user->user_type = 'client';
			$user->status = 1;
			$user->profile_picture = 'default.png';
			$user->save();
			
			//Update contacts with user_id
			$contact = Contact::where('contact_email',$user->email)
                              ->update(['user_id' => $user->id]);
			
			
			DB::commit();
			
			if($user->id > 0){ 
			   return redirect('login')->with('registration_success', _lang('Registration Sucessfully. You May Login to your account.'));
			}
		
		}
	}
}
