<?php

namespace AmiPraha\AILogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class AILogger extends AbstractProcessingHandler
{
    protected string $webhookUrl;

    public function __construct(string $webhookUrl, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->webhookUrl = $webhookUrl;
        parent::__construct($level, $bubble);
    }

    /**
     * Writes the log to the external webhook.
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $payload = [
            'level'     => $record['level_name'],
            'message'   => $record['message'],
            'context'   => $record['context'],
            'timestamp' => $record['datetime']->format('Y-m-d H:i:s'),
        ];

        try {
            // Basic cURL example to send JSON data
            $ch = curl_init($this->webhookUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e) {
            // If an error occurs while sending the log, you can decide how to handle it.
            // For instance, you could silently fail or log locally.
        }
    }
}
