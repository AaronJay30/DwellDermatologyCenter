<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Appointment; // make sure to import the Appointment model

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'date',
        'start_time',
        'end_time',
        'doctor_id',
        'is_booked',
        'consultation_fee',
    ];

    protected $casts = [
        'date' => 'date',
        'is_booked' => 'boolean',
        'consultation_fee' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    // Add this method for the appointments relationship
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'time_slot_id');
    }

    // Scope for available slots (not booked and no pending/active appointments)
    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false)
            ->whereDoesntHave('appointments', function($q) {
                $q->whereNotIn('status', ['cancelled']);
            });
    }

    // Scope for specific branch
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Scope for specific date
    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }
}
