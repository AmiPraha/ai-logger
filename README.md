# AI Logger

## Repository
**ami-praha/ai-logger**  
**Description:** A Laravel package providing a logging channel that sends log messages to an external webhook.

## Overview
This package adds a custom Laravel logging channel called `ai-logger`. When used, it sends log messages (and context data) as JSON payloads to a configured webhook URL.

## Installation
Require the package via Composer:

```sh
composer require ami-praha/ai-logger
```

No further configuration file publishing is necessary.

## Configuration
### Environment Variable
Set the webhook URL in your application's `.env` file:

```ini
AI_LOGGER_WEBHOOK_URL=https://ai-logger-webhook-endpoint
AI_LOGGER_SOURCE_CODE=https://your-application-code
AI_LOGGER_SOURCE_NAME=https://your-application-name
AI_LOGGER_SOURCE_URL=https://your-application-url
```

### Configuring Logging Channel
In Laravel’s `config/logging.php`, add a new channel `ai-logger`:

```php
// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        // other channels...

        'ai-logger' => [
            'driver' => 'ai-logger',
            'webhookUrl' => env('AI_LOGGER_WEBHOOK_URL', 'http://ai-logger.test/api/receive'),
            'sourceCode' => env('AI_LOGGER_SOURCE_CODE', 'EXAMPLEAPP'),
            'sourceName' => env('AI_LOGGER_SOURCE_NAME', 'Example App'),
            'sourceUrl' => env('AI_LOGGER_SOURCE_URL', 'http://example-app.test'),
            'level' => 'debug',
        ],
    ],
];
```

## Usage
To log using AI Logger, specify the `ai-logger` channel in your code:

```php
use Illuminate\Support\Facades\Log;

Log::channel('ai-logger')->info('Hello from AI Logger', ['extra_data' => 'test']);

// Other log levels work similarly
Log::channel('ai-logger')->error('Something went wrong.', [
    'exception' => 'Some exception info',
]);
```

This will send the log message, context data, log level, and timestamp as a JSON payload to the configured webhook URL.

## Verifying Functionality
1. **Set up a Test Webhook**: Use tools like [Webhook.site](https://webhook.site) or RequestBin to create a temporary webhook endpoint.
2. **Trigger a Log**: Execute a log statement:
   
   ```php
   Log::channel('ai-logger')->info('Testing AI Logger');
   ```

3. **Inspect Received Payload**: Confirm the JSON payload arrives at your test webhook.

## Requirements
- **PHP:** `^7.4 | ^8.0 | ^8.1 | ^8.2 | ^8.3`
- **Laravel:** `^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0`

## Contributing
Contributions are welcome!

1. Fork and create a feature branch.
2. Submit your changes through a Pull Request.
3. Provide a clear description of your updates.

For bugs or feature requests, please create an issue on GitHub.

## License
Licensed under the **MIT License**.

---
Thank you for using **AI Logger**! If you have questions, open an issue on GitHub or contact the maintainer.