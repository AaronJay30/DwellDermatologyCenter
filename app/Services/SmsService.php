<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send an SMS using a configurable HTTP API.
     *
     * Environment variables (set these in .env):
     *  SMS_ENABLED=true
     *  SMS_API_URL="https://your-sms-gateway.example.com/send"
     *  SMS_API_KEY="your-api-key-or-token"
     *  SMS_SENDER="DWELL"           (optional)
     *
     * The exact payload depends on your provider; this is a generic JSON
     * structure you can easily adapt on the gateway side.
     */
    public static function send(string $phoneNumber, string $message): void
    {
        if (!filter_var(env('SMS_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }

        $apiUrl = env('SMS_API_URL');
        $apiKey = env('SMS_API_KEY');

        if (!$apiUrl || !$apiKey) {
            Log::warning('SMS is enabled but SMS_API_URL or SMS_API_KEY is not configured.');
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->post($apiUrl, [
                'to' => $phoneNumber,
                'from' => env('SMS_SENDER', 'DWELL'),
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send SMS', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

