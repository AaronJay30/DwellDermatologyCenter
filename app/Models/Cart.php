<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'branch_id',
        'item_type',
        'quantity',
    ];

    protected $attributes = [
        'item_type' => 'service',
    ];

    public const TYPE_SERVICE = 'service';
    public const TYPE_CONSULTATION = 'consultation';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isConsultation(): bool
    {
        return $this->item_type === self::TYPE_CONSULTATION;
    }
}
