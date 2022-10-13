# Twilio Notification channel

This package adds a notification channel for Twilio services. You can use it to send SMS messages. Other channels might be available in the future.

## Installation

Install through Composer.

## Config

Add the following to your services config file.

```php
'twilio' => [
	'token' => env('TWILIO_TOKEN',''),
	'sid' => env('TWILIO_SID',''),
	'send' => env('TWILIO_SEND',false),
	'from' => env('TWILIO_FROM',''),
	'log' => env('TWILIO_LOG',true),
	'log_channel' => env('TWILIO_LOG_CHANNEL','stack'),
	'prefix' => '',
	'allow_multiple' => false,
],
```

- *log* if the messages should be written to a log file
- *log_channel* the log channel to log messages to
- *send* if the messages should be sent (production/dev environment)
- *bulglish* if cyrillic text should be converted to latin letters for SMS messages (cyrillic messages are limited to 67 characters)
- *allow_multiple* if SMS messages above 160 characters should be allowed (billed as multiple messages)

## Send test

To send a test message use the following artisan command:

`php artisan twilio:test phone --message='content' --channel=sms`

## Direct usage

You can instantiate a `Boyo\Twilio\TwilioMessage` object and send it immediately.

```php
use Boyo\Twilio\TwilioMessage;
use Boyo\Twilio\TwilioSender;

class MyClass
{
	public function myFunction()
	{
		$message = (new TwilioMessage())->to('359888888888')->sms('SMS text');
		
		$client = new TwilioSender();
		$client->send($message);	
	}
}
```

## Usage with notifications

1. Create a message file that extends `Boyo\Twilio\TwilioMessage`. It can take whatever data you need in the construct and should implement a `build()` method that defines the messages text content - a good practice would be to render a view file, so that your message content is in your views. You should only define the methods for the delivery channels that your are going to use. 

```php
use Boyo\Twilio\TwilioMessage;

class MyMessage extends TwilioMessage 
{
	public function __construct($data)
    {
        $this->id = $data->id; // your unique message id, add other parameters if needed
    }
    
	public function build() {
		// set your sms text 
		$this->sms('SMS text');
		
		return $this;
	}	
}
```

2. In your Notification class you can now include the Twilio channel in the `$via` array returned by the `via()` method.

```php
use Boyo\Twilio\TwilioChannel;

via($notifiable) 
{
	
	// ...
	
	$via[] = TwilioChannel::class;
	
	return $via 
	
}
```

Within the same Notification class you should also define a method `toSms()`:

```php
public function toSms($notifiable)
{
	return (new MyMessage($unique_id))->to($notifiable->phone);
}
```

The channel method is where you define the delivery channel you wish to use. 

- **sms** delivery by sms only (this is the default value, if you omit the channel method)
- other Twilio channels might be available in the future

