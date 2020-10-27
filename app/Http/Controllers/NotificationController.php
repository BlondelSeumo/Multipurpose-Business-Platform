<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationController extends Controller
{
	
	 /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_company_option('timezone', get_option('timezone','Asia/Dhaka'))); 

    }
    
    public function show($notificationid){
        $user = Auth::user();
		$notification = $user->notifications()->find($notificationid);
		if($notification) {
			$notification->markAsRead();
            
            if($user->user_type != 'client'){
                return redirect($notification->data['url']);
            }

            return redirect($notification->data['client_url']);
		}
	}

}
