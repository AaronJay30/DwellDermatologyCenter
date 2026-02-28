<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Models\Notification;
use Carbon\Carbon;
use App\Services\AppointmentNotificationLogger;

class NotifyTomorrowAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-tomorrow-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
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
        $this->info("Notifications created for tomorrow's appointments: " . count($notifications));
    }
}
