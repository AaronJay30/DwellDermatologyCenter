<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalInformation extends Model
{
    protected $fillable = [
        'user_id',
        'hypertension',
        'diabetes',
        'comorbidities_others',
        'allergies',
        'medications',
        'anesthetics',
        'anesthetics_others',
        'previous_hospitalizations_surgeries',
        'smoker',
        'alcoholic_drinker',
        'known_family_illnesses',
        'is_default',
    ];

    protected $casts = [
        'hypertension' => 'boolean',
        'diabetes' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getComorbiditiesListAttribute(): string
    {
        $comorbidities = [];
        if ($this->hypertension) {
            $comorbidities[] = 'Hypertension';
        }
        if ($this->diabetes) {
            $comorbidities[] = 'Diabetes';
        }
        if ($this->comorbidities_others) {
            $comorbidities[] = $this->comorbidities_others;
        }
        return implode(', ', $comorbidities) ?: 'None';
    }
}
