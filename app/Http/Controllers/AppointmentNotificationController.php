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
        foreach ($appointments as $appointment) {
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
