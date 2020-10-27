<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use App\EmailTemplate;
use App\Utilities\Overrider;

class LogVerifiedUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
		$user = $event->user;
        //Replace paremeter
		$replace = array(
			'{name}'=>$user->name,
			'{email}'=>$user->email,
			'{valid_to}' =>date('d M,Y', strtotime($user->valid_to)),
	    );
		
		//Send Welcome email
		Overrider::load("Settings");
		$template = EmailTemplate::where('name','registration')->first();
		$template->body = process_string($replace, $template->body);
		
		try{
			Mail::to($user->email)->send(new RegistrationMail($template));
		}catch (\Exception $e) {
			// Nothing
		}
    }
}
