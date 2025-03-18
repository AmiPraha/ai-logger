# AI Logger

**Repository:** `ami-praha/ai-logger`  
**Description:** A Laravel package providing a new logging channel that sends log messages to an external webhook.

## Overview
This package creates a custom logging driver in Laravel called `ai-logger`.  
When invoked, log messages (and optional context data) are sent as JSON payloads to a specified external webhook.

## Installation
### Require the package via Composer:
```bash
composer require ami-praha/ai-logger
```

### Publish the package configuration (optional but recommended):
```bash
php artisan vendor:publish --provider="AmiPraha\AILogger\AILoggerServiceProvider" --tag="config"
```
This command copies the `ai-logger.php` config file into your Laravel application’s `config` directory.

## Configuration
### Environment Variable
In your `.env` file, set the URL where the logs should be sent, for example:
```dotenv
AI_LOGGER_WEBHOOK_URL=https://your-external-webhook-endpoint
```
If not set, the default value from `config/ai-logger.php` (if published) will be used.

### Config/Logging
Laravel’s default logging configuration file is `config/logging.php`.  
Make sure to add or confirm a channel named `ai-logger`. Since the package’s service provider registers a channel named `ai-logger`, you simply need to reference it:

```php
// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        // other channels...

        'ai-logger' => [
            'driver' => 'ai-logger',
        ],
    ],
];
```

### Custom Settings
If desired, edit the published config file `config/ai-logger.php` to override default values (e.g., the `webhook_url`).

## Usage
To log with AI Logger, simply specify the `ai-logger` channel in your code:

```php
use Illuminate\Support\Facades\Log;

// Example: info-level log
Log::channel('ai-logger')->info('Hello from AI Logger', ['extra_data' => 'test']);

// You can also use other log levels like warning, error, debug, etc.
Log::channel('ai-logger')->error('Something went wrong.', [
    'exception' => 'Some exception info',
]);
```

When these log statements run, your Laravel app will send the log message, context data, log level, and timestamp to the configured `AI_LOGGER_WEBHOOK_URL` as a JSON payload.

## Verifying It Works
1. **Set up a Test Webhook:** If you do not have an existing webhook, set up a simple HTTP endpoint (e.g., using a tool like a local server or request bin) to inspect incoming requests.
2. **Trigger a Log:** Run `Log::channel('ai-logger')->info('Testing AI Logger');` in a controller or Tinker session.
3. **Check Incoming Request:** Confirm that your endpoint or third-party service received the JSON payload.

## Requirements
- **PHP:** `>= 7.4`
- **Laravel:** `^8.0, ^9.0, ^10.0, ^11.0, or ^12.0`

If you need explicit constraints for newer versions only, adjust the `composer.json` requirements to reflect your needs.

## Contributing
1. Fork the repository & create a new branch.
2. Make your changes.
3. Submit a pull request with an explanation of what you’ve changed.

Please open an issue if you find a bug or want to request a feature.

## License
This project is licensed under the **MIT License**.

Feel free to modify the code to suit your needs.

**Thank you for using AI Logger!**  
For additional help or questions, please open an issue on GitHub or contact the maintainer.
