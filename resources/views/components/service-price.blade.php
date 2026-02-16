@props([
    'pricing' => null,
    'layout' => 'default',
])

@php
    $pricing = $pricing ?? [];
    $hasPromo = data_get($pricing, 'has_promo', false);
    $original = (float) data_get($pricing, 'original_price', 0);
    $promo = data_get($pricing, 'promo_price');
    $discount = data_get($pricing, 'discount_percent');
    $classes = 'price-display' . ($layout === 'compact' ? ' price-compact' : '');
@endphp

<div class="{{ $classes }}">
    @if($hasPromo && $promo !== null)
        <span class="price-original">₱{{ number_format($original, 2) }}</span>
        <span class="price-promo">₱{{ number_format($promo, 2) }}</span>
        @if(!is_null($discount))
            <span class="price-discount">-{{ number_format($discount, 0) }}%</span>
        @endif
    @else
        <span class="price-regular">₱{{ number_format($original, 2) }}</span>
    @endif
</div>

