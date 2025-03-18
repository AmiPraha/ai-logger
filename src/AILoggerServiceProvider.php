<?php

namespace YourVendor\AILogger;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class AILoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge package config
        $this->mergeConfigFrom(
            __DIR__ . '/config/ai-logger.php', 'ai-logger'
        );

        // Register the custom logger channel
        $this->app->bind('logging.channels.ai-logger', function($app) {
            return [
                'driver' => 'monolog',
                'handler' => AILogger::class,
                'with' => [
                    'webhookUrl' => config('ai-logger.webhook_url'),
                ],
                'level' => 'debug', // Adjust if you prefer a different log level
            ];
        });
    }

    public function boot()
    {
        // Publish the config so it can be copied to the main /config folder
        $this->publishes([
            __DIR__ . '/config/ai-logger.php' => config_path('ai-logger.php'),
        ], 'config');
    }
}
