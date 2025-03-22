<?php

namespace AmiPraha\AILogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;

class AILogger extends AbstractProcessingHandler
{
    protected ?string $webhookUrl;
    protected ?string $sourceCode;
    protected ?string $sourceName;
    protected ?string $sourceUrl;

    public function __construct(?string $webhookUrl = null, ?string $sourceCode = null, ?string $sourceName = null, ?string $sourceUrl = null, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->webhookUrl = $webhookUrl;
        $this->sourceCode = $sourceCode;
        $this->sourceName = $sourceName;
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * Writes the log to the external webhook.
     *
     * @param LogRecord $record
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        if (empty($this->webhookUrl) || empty($this->sourceCode) || empty($this->sourceName) || empty($this->sourceUrl)) {
            $this->logInternalError('webhookUrl, sourceCode, sourceName, or sourceUrl is missing, please add all of them to your config. See ai-logger documentation for more info.');

            return;
        }

        $payload = [
            'level'     => $record->level->getName(),
            'message'   => $record->message,
            'context'   => $record->context,
            'timestamp' => $record->datetime->format('Y-m-d H:i:s'),
            'source'    => [
                'code' => $this->sourceCode,
                'name' => $this->sourceName,
                'url'  => $this->sourceUrl,
            ]
        ];

        $jsonPayload = json_encode($payload);

        if ($jsonPayload === false) {
            $this->logInternalError('JSON encoding error - ' . json_last_error_msg());

            return;
        }

        $ch = curl_init($this->webhookUrl);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 2,
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->logInternalError('cURL error - ' . curl_error($ch) . '. Probably invalid webhook URL.');
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpStatusCode < 200 || $httpStatusCode >= 300) {
                $this->logInternalError('HTTP error', [
                    'status_code' => $httpStatusCode,
                    'response'    => $response,
                ]);
            }
        }

        curl_close($ch);
    }

    /**
     * Writes internal errors to a dedicated log file, bypassing Laravel's logging.
     *
     * @param string $message
     * @param array  $context
     * @return void
     */
    protected function logInternalError(string $message, array $context = []): void
    {
        // Build a log line with a timestamp, the error level, message, and optional context.
        $timestamp = date('Y-m-d H:i:s');
        $line = "[$timestamp] [AILogger INTERNAL ERROR] $message";

        // If there is context data, you can dump it as JSON (or var_export) and append it
        if (!empty($context)) {
            $contextJson = json_encode($context);
            $line .= " | context: " . ($contextJson === false ? print_r($context, true) : $contextJson);
        }

        $line .= "\n";

        // Write to a dedicated file. Adjust the path to wherever you want your package’s error file to live.
        // If this package runs under Laravel, you could do:
        //   file_put_contents(storage_path('logs/ailogger_critical.log'), $line, FILE_APPEND);
        //
        // But if you want the package to be self-contained (without relying on storage_path()),
        // you can do something like:
        file_put_contents(storage_path('logs/ai_logger_critical.log'), $line, FILE_APPEND);
    }
}
