<?php
namespace Boyo\Twilio;

use Illuminate\Support\ServiceProvider;

class TwilioServiceProvider extends ServiceProvider
{
	
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
	        $this->commands([
	            \Boyo\Twilio\Commands\Test::class,
	        ]);
	    }
    }
    
    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(\Boyo\Twilio\TwilioSender::class, function () {
            return new \Boyo\Twilio\TwilioSender();
        });
    }
    
}