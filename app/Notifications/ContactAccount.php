<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ContactAccount extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
	private $account;
	
    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
		$login_url = url('login');
		
        return (new MailMessage)
		            ->subject(_lang('Client Portal Access'))
                    ->line(_lang('Your email has registered to our client portal.'))
                    ->line(_lang('You can now login to your client portal using following details.'))
                    ->line(_lang('Email').': '.$this->account->email)
                    ->line(_lang('Password').': '.$this->account->password)
                    ->action(_lang('Login to Portal'), $login_url)
                    ->line(_lang('Thank you for joining with us!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
