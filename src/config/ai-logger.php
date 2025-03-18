<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External Webhook URL
    |--------------------------------------------------------------------------
    | The URL where log messages should be sent via AI Logger.
    */
    'webhook_url' => env('AI_LOGGER_WEBHOOK_URL', 'https://example.com/ai-logger'),
];
