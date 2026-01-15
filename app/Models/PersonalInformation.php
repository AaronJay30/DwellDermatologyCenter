<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalInformation extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_initial',
        'last_name',
        'address',
        'birthday',
        'civil_status',
        'preferred_pronoun',
        'contact_number',
        'label',
        'is_default',
        'signature',
    ];

    protected $casts = [
        'birthday' => 'date',
        'is_default' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        $name = $this->first_name;
        if ($this->middle_initial) {
            $name .= ' ' . $this->middle_initial . '.';
        }
        $name .= ' ' . $this->last_name;
        return $name;
    }
}
