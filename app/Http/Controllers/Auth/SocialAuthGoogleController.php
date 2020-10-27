<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use App\Services\SocialGoogleAccountService;
use App\Utilities\Overrider;

class SocialAuthGoogleController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		Overrider::load("ServiceSettings");
    }
	
   /**
   * Create a redirect method to google api.
   *
   * @return void
   */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
	
	/**
     * Return a callback method from google api.
     *
     * @return callback URL from google
     */
    public function callback(SocialGoogleAccountService $service)
    {
        $user = $service->createOrGetUser(Socialite::driver('google')->user());
		if($user != NULL){
			auth()->login($user);
			return redirect()->to('/dashboard');
		}
		return redirect()->to('/login')->with('error',_lang('Sorry, We did not find any account associated with your email !'));
    }
}
