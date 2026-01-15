<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule appointment reminders to run every 5 minutes
Schedule::command('appointments:send-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

// Schedule promo expiration to run every hour
Schedule::command('promos:expire')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground();
