<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	protected function credentials(Request $request){
		return [
			'email' => $request->{$this->username()},
			'password' => $request->password,
			'status' => 1,
		];
	}
	
	
	protected function authenticated(Request $request, $user)
	{
	
		if($user->user_type == "staff"){	
			$company = $user->company;  
		    if ($company->status != 1) {
				$errors = [$this->username() => _lang('Your company account is not active !')];
			    Auth::logout();
				return back()->withInput($request->only($this->username(), 'remember'))
							 ->withErrors($errors);
			}	
		}

        //Store Session Data
        if($user->user_type != "admin"){
            if($user->user_type != "client"){
    			$request->session()->put('company_id', $user->company_id);
            }else{
                $company = Auth::user()->client->first();
				if($company){
					$request->session()->put('company_id', $company->company_id);
				}
            }
		}

        $request->session()->put('user_language', $user->language);		
	}
	
	/**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        // Load user from database
        $user = \App\User::where($this->username(), $request->{$this->username()})->first();

		if ($user && \Hash::check($request->password, $user->password) && $user->status != 1) {
			$errors = [$this->username() => _lang('Your account is not active !')];
		}
		
        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }
        return back()->withInput($request->only($this->username(), 'remember'))
					 ->withErrors($errors);
    }
}
