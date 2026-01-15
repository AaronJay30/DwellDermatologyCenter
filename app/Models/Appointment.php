<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $fillable = [
        'patient_id',
        'personal_information_id',
        'doctor_id',
        'service_id',
        'doctor_slot_id',
        'status',
        'notes',
        'admin_note',
        'first_name',
        'middle_initial',
        'last_name',
        'age',
        'consultation_type',
        'description',
        'medical_background',
        'referral_source',
        'consultation_fee',
        'branch_id',
        'time_slot_id',
        'cancellation_reason',
        'scheduled_date',
        'scheduled_time',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function doctorSlot(): BelongsTo
    {
        return $this->belongsTo(DoctorSlot::class);
    }

    public function patientHistory(): BelongsTo
    {
        return $this->belongsTo(PatientHistory::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function personalInformation(): BelongsTo
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
