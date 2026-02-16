<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromoService extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'service_id',
        'original_price',
        'promo_price',
        'discount_percent',
    ];

    protected $casts = [
        'original_price' => 'decimal:2',
        'promo_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    // Calculate promo price from discount percent
    public function calculatePromoPrice()
    {
        if ($this->discount_percent) {
            $this->promo_price = $this->original_price * (1 - ($this->discount_percent / 100));
            $this->save();
        }
    }

    // Calculate discount percent from promo price
    public function calculateDiscountPercent()
    {
        if ($this->promo_price && $this->original_price > 0) {
            $this->discount_percent = (($this->original_price - $this->promo_price) / $this->original_price) * 100;
            $this->save();
        }
    }
}
