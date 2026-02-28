<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AppointmentNotificationLogger
{
    protected static $logFile = 'appointment_notifications.log';

    public static function log($message, $context = [])
    {
        $logPath = storage_path('logs/' . self::$logFile);
        $date = now()->toDateTimeString();
        $contextString = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$date] $message $contextString" . PHP_EOL;
        file_put_contents($logPath, $logMessage, FILE_APPEND);
    }
}
