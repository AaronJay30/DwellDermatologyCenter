<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientHistory extends Model
{
    protected $fillable = [
        'patient_id',
        'personal_information_id',
        'doctor_id',
        'appointment_id',
        'consultation_result',
        'prescription',
        'follow_up_required',
        'follow_up_date',
        'notes',
        'treatment_notes',
        'diagnosis',
        'outcome',
        'treatment_date',
    ];

    protected function casts(): array
    {
        return [
            'treatment_date' => 'date',
            'follow_up_date' => 'date',
            'follow_up_required' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function personalInformation(): BelongsTo
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
