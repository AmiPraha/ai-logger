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
            // Convert the string level to Monolog's Level
            $monologLevel = Logger::toMonologLevel($config['level'] ?? 'debug');

            return new Logger('ai-logger', [
                new AILogger(
                    $config['webhookUrl'] ?? null,
                    $config['sourceCode'] ?? null,
                    $config['sourceName'] ?? null,
                    $config['sourceUrl'] ?? null,
                    $config['sourceGitProjectOwner'] ?? null,
                    $config['sourceGitProjectName'] ?? null,
                    $config['sourceJiraProjectCode'] ?? null,
                    $config['sourceJiraParentProjectKey'] ?? null,
                    $monologLevel,
                    $config['bubble'] ?? true
                ),
            ]);
        });
    }
}
