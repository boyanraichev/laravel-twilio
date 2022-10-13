<?php
namespace Boyo\Twilio\Commands;

use Illuminate\Console\Command;
use Boyo\Twilio\TwilioSender;
use Boyo\Twilio\TwilioMessage;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twilio:test {phone : Phone to send to} {--channel=sms} {--message=test}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test message';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    
	    try {
		    
		    $phone = $this->argument('phone');
		    
		    $channel = $this->option('channel');
		    
		    $content = $this->option('message');
            
		    $message = (new TwilioMessage(time()))->to($phone)->sms($content);
		    
		    if (!empty($channel)) {
			    $message->channel($channel);
		    }
		    
		    $client = new TwilioSender();
		    
		    $client->forceSend($message);
		    
	        $this->info('Message send');
			
		} catch(\Exception $e) {
			
			$this->error($e->getMessage());
			
		}
    }
}