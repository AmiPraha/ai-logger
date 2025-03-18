<?php

namespace AmiPraha\AILogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Monolog\LogRecord;
use Illuminate\Support\Facades\Log;

class AILogger extends AbstractProcessingHandler
{
    protected string $webhookUrl;

    public function __construct(string $webhookUrl, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->webhookUrl = $webhookUrl;
    }

    /**
     * Writes the log to the external webhook.
     *
     * @param LogRecord $record
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        $payload = [
            'level'     => $record->level->getName(),
            'message'   => $record->message,
            'context'   => $record->context,
            'timestamp' => $record->datetime->format('Y-m-d H:i:s'),
        ];

        $jsonPayload = json_encode($payload);

        if ($jsonPayload === false) {
            Log::error('AILogger: JSON encoding error - ' . json_last_error_msg());
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
            Log::error('AILogger: cURL error - ' . curl_error($ch));
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpStatusCode < 200 || $httpStatusCode >= 300) {
                Log::error('AILogger: HTTP error', [
                    'status_code' => $httpStatusCode,
                    'response'    => $response,
                ]);
            }
        }

        curl_close($ch);
    }
}