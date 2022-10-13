<?php

namespace Boyo\Twilio;

use Illuminate\Notifications\Notification;
use Boyo\Twilio\TwilioSender;
use Boyo\Twilio\TwilioMessage;

class TwilioChannel
{
	
    protected $client;
    
    public function __construct()
    {

    }
    
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        
        $message = $notification->toSms($notifiable);
        
        if (!$message instanceof TwilioMessage) {
	        throw new \Exception('No message provided');
	    }
	    
	    if (method_exists($message, 'build')) {
            $message->build();
        }
	    
        $client = new TwilioSender();
        
        $client->send($message);
        
    }
    
    
}