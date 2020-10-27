<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailSubscriber;
use Validator;
use Auth;

class EmailSubscriberController extends Controller
{
	
	public function __construct()
    {	
		date_default_timezone_set(get_option('timezone','Asia/Dhaka'));	
    }
   
    public function index()
    {
		$email_subscribers = EmailSubscriber::orderBy('id','desc')
		                                    ->get();
		return view('backend.email_subscriber.list',compact('email_subscribers'));
    }
	
}
