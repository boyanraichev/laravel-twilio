<?php

namespace Boyo\Twilio;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Boyo\Twilio\Exceptions\CouldNotSendMessage;

class TwilioSender
{
	private $log = true;
	
	private $log_channel = 'stack';
	
	private $send = false;
	
	private $token = null;
	
	private $sid = null;
	
	private $from = null;
	
	public function __construct() {
		
		$this->token = config('services.twilio.token');
		$this->sid = config('services.twilio.sid');
		
		$this->send = config('services.twilio.send');
		$this->from = config('services.twilio.from');
		
		$this->log = config('services.twilio.log');
		$this->log_channel = config('services.twilio.log_channel');
		
		$this->client = new Client($this->sid, $this->token);
		
	}
	
	public function forceSend(TwilioMessage $message) {
		
		$this->send = true;
		
		$this->send($message);
		
		return $this;
		
	}
	
	// send email
	public function send(TwilioMessage $message) {
		
		try {
			
			$request = $message->getMessage();
				
			if ($this->log) {
				$call_id = Str::random(10);
				Log::channel($this->log_channel)->info("Twilio message {$call_id}",$request);
			}
			
			if ($this->send) {
				
				$response = $this->client->messages->create(
					$request['to'],
					[
						'from' => $this->from,
						'body' => $request['body'],
					],
				);
				
				$result = (array) $response;
				
				if ($this->log) {
					Log::channel($this->log_channel)->info("Twilio response {$call_id}",$result);
				}
				
				if (!$response || !empty($response->errorCode)) {
					throw new CouldNotSendMessage($response->errorMessage ?? '');
				}
				
			}
			
		} catch(\Exception $e) {
			
			Log::channel($this->log_channel)->info('Could not send Twilio message ('.$e->getMessage().')');
			
		}
		
	}
	
	
}
