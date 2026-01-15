<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic reminders 1 hour before appointments and consultations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for appointments and consultations that need reminders...');

        // Calculate the time range: 1 hour from now (with 5 minute window)
        $oneHourFromNow = Carbon::now()->addHour();
        $oneHourFiveMinutesFromNow = Carbon::now()->addHour()->addMinutes(5);

        // Get appointments that:
        // 1. Are confirmed or booked (status: 'confirmed', 'booked')
        // 2. Have a time slot
        // 3. Are scheduled between 1 hour and 1 hour 5 minutes from now
        // 4. Haven't received a reminder yet (check by looking for existing reminder notifications)
        
        $appointments = Appointment::whereIn('status', ['confirmed', 'booked'])
            ->whereNotNull('time_slot_id')
            ->whereHas('timeSlot', function ($query) use ($oneHourFromNow, $oneHourFiveMinutesFromNow) {
                $query->whereDate('date', $oneHourFromNow->toDateString())
                    ->whereTime('start_time', '>=', $oneHourFromNow->format('H:i:s'))
                    ->whereTime('start_time', '<=', $oneHourFiveMinutesFromNow->format('H:i:s'));
            })
            ->with(['timeSlot', 'patient', 'doctor', 'branch', 'service'])
            ->get();

        $remindersSent = 0;

        foreach ($appointments as $appointment) {
            if (!$appointment->timeSlot || !$appointment->patient_id) {
                continue;
            }

            // Check if a reminder has already been sent for this appointment
            $existingReminder = Notification::where('user_id', $appointment->patient_id)
                ->where('type', 'reminder')
                ->where('message', 'like', '%' . $appointment->timeSlot->date->format('M d, Y') . '%')
                ->where('message', 'like', '%' . $appointment->timeSlot->start_time . '%')
                ->where('created_at', '>=', Carbon::now()->subHours(2)) // Check last 2 hours
                ->exists();

            if (!$existingReminder) {
                // Use the helper method from NotificationService
                NotificationService::sendAppointmentReminderForTimeSlot($appointment);

                $remindersSent++;
                $this->info("Sent reminder to patient ID {$appointment->patient_id} for appointment ID {$appointment->id}");
            }
        }

        $this->info("Completed! Sent {$remindersSent} reminder(s).");
        
        return Command::SUCCESS;
    }
}

