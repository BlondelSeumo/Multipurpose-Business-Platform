<?php

namespace App\Services;
use App\SocialGoogleAccount;
use App\User;
use App\Company;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Hash;
use DB;

class SocialGoogleAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {
        $account = SocialGoogleAccount::whereProvider('google')
									  ->whereProviderUserId($providerUser->getId())
									  ->first();
		if ($account) {
			return $account->user;
		} else {
			$account = new SocialGoogleAccount([
								'provider_user_id' => $providerUser->getId(),
								'provider' => 'google'
							]);
			$user = User::whereEmail($providerUser->getEmail())->first();
			
			if (!$user) {	
				
				/*$trial_period = get_option('trial_period',7);
		
				if($trial_period < 1){
					$valid_to = date('Y-m-d', strtotime(date('Y-m-d'). " -1 day"));
				}else{
					$valid_to = date('Y-m-d', strtotime(date('Y-m-d'). " + $trial_period days"));
				}
				
				$user= new User();
				$user->name = $providerUser->getName();
				$user->email = $providerUser->getEmail();
				$user->email_verified_at = date('Y-m-d H:i:s');
				$user->password = Hash::make(rand());
				$user->user_type = 'user';
				$user->status = 1;
				$user->valid_to = $valid_to;
				$user->profile_picture = 'default.png';
				$user->currency = '$';
				$user->membership_type = 'trial';
				$user->save();*/
				
				return NULL;
				
			}
			
			$account->user()->associate($user);
			$account->save();
			return $user;
        }
    }
}