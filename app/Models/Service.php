<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'duration_minutes',
        'photo_path',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('display_order');
    }

    public function promoServices(): HasMany
    {
        return $this->hasMany(PromoService::class);
    }

    protected array $pricingCache = [];

    public function getPricingAttribute(): array
    {
        if (empty($this->pricingCache)) {
            $this->pricingCache = $this->calculatePricing();
        }

        return $this->pricingCache;
    }

    public function hasActivePromo(): bool
    {
        return $this->pricing['has_promo'] ?? false;
    }

    public function getDisplayPriceAttribute(): float
    {
        return $this->pricing['display_price'] ?? (float) $this->price;
    }

    protected function calculatePricing(): array
    {
        $basePrice = (float) $this->price;
        $activePromo = $this->resolveActivePromoService();

        if (!$activePromo) {
            return [
                'has_promo' => false,
                'original_price' => $basePrice,
                'promo_price' => null,
                'discount_percent' => null,
                'display_price' => $basePrice,
                'promotion' => null,
            ];
        }

        $originalPrice = (float) ($activePromo->original_price ?? $basePrice);
        $promoPrice = $activePromo->promo_price !== null ? (float) $activePromo->promo_price : null;
        $discountPercent = $activePromo->discount_percent !== null ? (float) $activePromo->discount_percent : null;

        if ($promoPrice === null && $discountPercent !== null) {
            $promoPrice = round($originalPrice - ($originalPrice * ($discountPercent / 100)), 2);
        }

        if ($discountPercent === null && $promoPrice !== null && $originalPrice > 0) {
            $discountPercent = round((($originalPrice - $promoPrice) / $originalPrice) * 100, 2);
        }

        if ($promoPrice === null || $promoPrice >= $originalPrice) {
            return [
                'has_promo' => false,
                'original_price' => $originalPrice,
                'promo_price' => null,
                'discount_percent' => null,
                'display_price' => $originalPrice,
                'promotion' => null,
            ];
        }

        return [
            'has_promo' => true,
            'original_price' => $originalPrice,
            'promo_price' => $promoPrice,
            'discount_percent' => $discountPercent,
            'display_price' => $promoPrice,
            'promotion' => $activePromo->promotion ? [
                'id' => $activePromo->promotion->id,
                'title' => $activePromo->promotion->display_title,
                'code' => $activePromo->promotion->promo_code,
            ] : null,
        ];
    }

    protected function resolveActivePromoService()
    {
        $promoServices = $this->relationLoaded('promoServices')
            ? $this->promoServices
            : $this->promoServices()->with('promotion')->get();

        if (!$promoServices instanceof Collection || $promoServices->isEmpty()) {
            return null;
        }

        return $promoServices
            ->filter(function ($promoService) {
                return $promoService->promotion && $promoService->promotion->isActive();
            })
            ->sortByDesc(function ($promoService) {
                $promotion = $promoService->promotion;
                return $promotion->starts_at ?? $promotion->created_at ?? $promoService->updated_at ?? $promoService->created_at;
            })
            ->first();
    }
}
