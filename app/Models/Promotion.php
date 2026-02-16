<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
        'status',
        'promo_code',
        'max_claims_per_patient',
        'branch_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'status' => 'string',
        'max_claims_per_patient' => 'integer',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(PromotionImage::class)->orderBy('display_order');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'promo_services')
            ->withPivot('original_price', 'promo_price', 'discount_percent')
            ->withTimestamps();
    }

    public function promoServices(): HasMany
    {
        return $this->hasMany(PromoService::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where(function($q) {
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'active')
            ->where('is_active', true)
            ->where('starts_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere(function($q2) {
                  $q2->whereNotNull('ends_at')
                     ->where('ends_at', '<', now());
              });
        });
    }

    // Auto-update status based on dates
    public function updateStatus()
    {
        $now = now();
        
        if ($this->ends_at && $this->ends_at < $now) {
            $this->status = 'expired';
            $this->is_active = false;
        } elseif ($this->starts_at && $this->starts_at > $now) {
            $this->status = 'upcoming';
        } elseif ($this->status !== 'active') {
            $this->status = 'active';
        }
        
        $this->save();
    }

    public function getDisplayTitleAttribute()
    {
        return $this->title ?? $this->name;
    }

    public function isActive(): bool
    {
        if (!$this->is_active || $this->status !== 'active') {
            return false;
        }

        $now = now();
        
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }
        
        if ($this->ends_at && $this->ends_at < $now) {
            return false;
        }
        
        return true;
    }
}
