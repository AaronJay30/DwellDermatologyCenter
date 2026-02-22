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
    protected $description = 'Send automatic reminders 1 day before appointments and consultations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for appointments and consultations that need reminders...');

        // Appointments scheduled for TOMORROW (1 day from now) - send reminder 1 day before
        $tomorrow = Carbon::tomorrow();

        // Get appointments that:
        // 1. Are confirmed or booked (status: 'confirmed', 'booked')
        // 2. Have a time slot
        // 3. Are scheduled for tomorrow
        // 4. Haven't received a reminder yet (check by looking for existing reminder notifications)
        
        $appointments = Appointment::whereIn('status', ['confirmed', 'booked'])
            ->whereNotNull('time_slot_id')
            ->whereHas('timeSlot', function ($query) use ($tomorrow) {
                $query->whereDate('date', $tomorrow->toDateString());
            })
            ->with(['timeSlot', 'patient', 'doctor', 'branch', 'service'])
            ->get();

        $remindersSent = 0;

        foreach ($appointments as $appointment) {
            if (!$appointment->timeSlot || !$appointment->patient_id) {
                continue;
            }

            // Check if a reminder has already been sent for this appointment (check last 2 days)
            $existingReminder = Notification::where(function ($q) use ($appointment) {
                $q->where('user_id', $appointment->patient_id)
                    ->orWhere('user_id', $appointment->doctor_id);
            })
                ->where('type', 'reminder')
                ->where('message', 'like', '%' . $appointment->timeSlot->date->format('M d, Y') . '%')
                ->where('message', 'like', '%' . $appointment->timeSlot->start_time . '%')
                ->where('created_at', '>=', Carbon::now()->subDays(2))
                ->exists();

            if (!$existingReminder) {
                // Send reminder to BOTH patient and doctor
                NotificationService::sendAppointmentReminderForTimeSlot($appointment);

                $remindersSent++;
                $this->info("Sent reminder to patient ID {$appointment->patient_id} and doctor ID " . ($appointment->doctor_id ?? 'N/A') . " for appointment ID {$appointment->id}");
            }
        }

        $this->info("Completed! Sent {$remindersSent} reminder(s).");
        
        return Command::SUCCESS;
    }
}

