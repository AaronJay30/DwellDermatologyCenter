<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Http\Controllers\AppointmentNotificationController;
use App\Services\AppointmentNotificationLogger;

class SendAppointmentNotificationsOnLogin
{
    public function handle(Login $event): void
    {
        AppointmentNotificationLogger::log('Login triggered appointment notification check', [
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
        ]);

        try {
            (new AppointmentNotificationController())->notifyTomorrowAppointments();
            AppointmentNotificationLogger::log('Appointment notification check completed');
        } catch (\Exception $e) {
            AppointmentNotificationLogger::log('Appointment notification check failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
