<?php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Models\Notification;

class NotificationService
{
    public static function sendNotification($title, $message, $type = 'announcement', $userId = null)
    {
        $notification = Notification::create([
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'user_id' => $userId,
        ]);

        // Broadcast the notification in real-time
        event(new NotificationCreated($notification));

        return $notification;
    }

    public static function sendAppointmentReminder($appointment)
    {
        return self::sendNotification(
            'Appointment Reminder',
            "You have an appointment scheduled for {$appointment->doctorSlot->slot_date->format('M d, Y')} at {$appointment->doctorSlot->start_time} with Dr. {$appointment->doctor->name}.",
            'reminder',
            $appointment->patient_id
        );
    }

    public static function sendAppointmentConfirmation($appointment)
    {
        return self::sendNotification(
            'Appointment Confirmed',
            "Your appointment has been confirmed for {$appointment->doctorSlot->slot_date->format('M d, Y')} at {$appointment->doctorSlot->start_time} with Dr. {$appointment->doctor->name}.",
            'reminder',
            $appointment->patient_id
        );
    }

    public static function sendPromotion($title, $message, $userId = null)
    {
        return self::sendNotification(
            $title,
            $message,
            'promotion',
            $userId
        );
    }

    public static function sendSystemAnnouncement($title, $message, $userId = null)
    {
        return self::sendNotification(
            $title,
            $message,
            'system',
            $userId
        );
    }

    public static function sendGlobalAnnouncement($title, $message)
    {
        return self::sendNotification(
            $title,
            $message,
            'announcement',
            null
        );
    }

    public static function sendConsultationRequest($appointment)
    {
        return self::sendNotification(
            'New Consultation Request',
            "A new consultation has been requested by {$appointment->first_name} {$appointment->last_name} for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time}. Consultation type: {$appointment->consultation_type}",
            'system',
            $appointment->doctor_id
        );
    }

    public static function sendConsultationConfirmation($appointment, $notes = null)
    {
        $message = "Your consultation request for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time} has been confirmed.";
        if ($notes) {
            $message .= " Notes: {$notes}";
        }

        return self::sendNotification(
            'Consultation Confirmed',
            $message,
            'reminder',
            $appointment->patient_id
        );
    }

    public static function sendConsultationCancellation($appointment, $reason)
    {
        // Build message based on whether timeSlot exists
        if ($appointment->timeSlot) {
            $message = "Your consultation request for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time} has been cancelled. Reason: {$reason}";
        } else {
            $message = "Your consultation request has been cancelled. Reason: {$reason}";
        }

        return self::sendNotification(
            'Consultation Cancelled',
            $message,
            'system',
            $appointment->patient_id
        );
    }

    public static function sendAppointmentReminderForTimeSlot($appointment)
    {
        $isConsultation = !empty($appointment->consultation_type);
        
        if ($isConsultation) {
            $message = "Reminder: You have a consultation scheduled for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time}";
            if ($appointment->branch) {
                $message .= " at {$appointment->branch->name}";
            }
            if ($appointment->consultation_type) {
                $message .= ". Consultation type: {$appointment->consultation_type}";
            }
        } else {
            $serviceName = $appointment->service ? $appointment->service->name : 'your appointment';
            $message = "Reminder: You have an appointment for {$serviceName} scheduled for {$appointment->timeSlot->date->format('M d, Y')} at {$appointment->timeSlot->start_time}";
            if ($appointment->branch) {
                $message .= " at {$appointment->branch->name}";
            }
            if ($appointment->doctor) {
                $message .= " with Dr. {$appointment->doctor->name}";
            }
        }

        return self::sendNotification(
            'Appointment Reminder',
            $message,
            'reminder',
            $appointment->patient_id
        );
    }
}
