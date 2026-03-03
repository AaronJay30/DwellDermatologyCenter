<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use App\Services\AppointmentNotificationLogger;

class AppointmentNotificationController extends Controller
{
    public function notifyTomorrowAppointments()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $appointments = Appointment::whereDate('scheduled_date', $tomorrow)->get();
        $notifications = [];

        if ($appointments->isEmpty()) {
            AppointmentNotificationLogger::log('No appointments found for tomorrow', [
                'scheduled_date' => $tomorrow,
                'count' => 0,
            ]);
        }

        foreach ($appointments as $appointment) {
            $alreadyNotified = Notification::where('user_id', $appointment->patient_id)
                ->where('type', 'appointment')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadyNotified) {
                AppointmentNotificationLogger::log('Skipping duplicate notification', [
                    'user_id' => $appointment->patient_id,
                    'appointment_id' => $appointment->id,
                    'scheduled_date' => $appointment->scheduled_date,
                ]);
                continue;
            }

            $notification = Notification::create([
                'user_id' => $appointment->patient_id,
                'title' => 'Upcoming Appointment',
                'message' => 'You have an appointment scheduled for tomorrow.',
                'type' => 'appointment',
            ]);
            $notifications[] = $notification;
            AppointmentNotificationLogger::log('Notification created', [
                'user_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'notification_id' => $notification->id,
                'scheduled_date' => $appointment->scheduled_date,
            ]);
        }
        return response()->json([
            'message' => "Notifications created for tomorrow's appointments.",
            'count' => count($notifications),
        ]);
    }
}
