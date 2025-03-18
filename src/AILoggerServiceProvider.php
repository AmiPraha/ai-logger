<?php

namespace AmiPraha\AILogger;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Illuminate\Support\Facades\Log;

class AILoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 
    }

    public function boot()
    {
        // Register custom logging channel via Log::extend()
        Log::extend('ai-logger', function ($app, array $config) {
            return new Logger('ai-logger', [
                new AILogger($config['webhookUrl']),
            ]);
        });
    }
}
