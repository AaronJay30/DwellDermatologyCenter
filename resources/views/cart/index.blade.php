@extends('layouts.patient')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
@endpush
@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="card" style="text-align: center; padding: 2rem 1rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #e9ecef); color: var(--dark-text, #2c3e50);">
            <h3 style="color: var(--light-text); margin-bottom: 1rem; font-size: 1.25rem;">Your cart is empty</h3>
            <p style="color: var(--light-text); margin-bottom: 2rem; font-size: 0.95rem;">Add some services to get started!</p>
        </div>
    @else
        @php
            $multipleBranches = $itemsByBranch->count() > 1;
        @endphp
        
        @if($multipleBranches)
            <div class="notice-box" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #856404;">
                <strong> Notice:</strong> Your cart contains services from different branches. Items are grouped by branch below. You can select items from one or more branches to checkout.
            </div>
        @endif

        <div class="cart-grid">
            <!-- Cart Items -->
            <div class="card" style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #e9ecef); color: var(--dark-text, #2c3e50);">
                <h2 style="color: var(--primary-color); margin-bottom: 1.5rem;">Cart Items</h2>
                
                @foreach($itemsByBranch as $branchId => $branchItems)
                    @php
                        $branch = $branchItems->first()->service->category->branch ?? null;
                    @endphp
                    
                    @if($branch)
                        <!-- Branch Header -->
                        <div class="branch-header" style="background: #f8f9fa; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid var(--primary-color); border-radius: 4px;">
                            <h3 style="color: var(--primary-color); margin: 0; font-size: 1.1rem; font-weight: bold;">
                                 {{ $branch->name }}
                            </h3>
                            @if($branch->address)
                                <p style="color: var(--light-text); margin: 0.25rem 0 0 0; font-size: 0.9rem;">{{ $branch->address }}</p>
                            @endif
                        </div>
                    @endif
                    
                    @foreach($branchItems as $item)
                        @php($pricing = $item->service->pricing)
                        <div class="cart-item" data-cart-id="{{ $item->id }}" data-price="{{ $pricing['display_price'] }}" data-quantity="{{ $item->quantity }}">
                            
                            <!-- Checkbox -->
                            <div style="flex-shrink: 0;">
                                <input type="checkbox" class="cart-item-checkbox" data-cart-id="{{ $item->id }}" checked style="width: 20px; height: 20px; cursor: pointer;">
                            </div>
                            
                            <!-- Service Image -->
                            <div class="cart-item-image" style="flex-shrink: 0;">
                                @php($firstImage = $item->service->images->first())
                                @if($firstImage)
                                    <img src="{{ asset('storage/' . $firstImage->image_path) }}" 
                                        alt="{{ $item->service->name }}" 
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                @else
                                    <img src="{{ asset('images/default-service.png') }}" 
                                        alt="No image available" 
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                @endif
                            </div>

                            <!-- Service Details -->
                            <div class="cart-item-details" style="flex: 1;">
                                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem; font-size: 1.1rem;">{{ $item->service->name }}</h3>
                                <p style="color: var(--light-text); margin-bottom: 0.5rem; font-size: 0.9rem;">{{ Str::limit($item->service->description ?? 'No description available', 100) }}</p>
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                                    <span style="background: var(--primary-color); color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.8rem;">
                                        {{ $item->service->category->name ?? 'Uncategorized' }}
                                    </span>
                                    @if($branch)
                                        <span style="background: #6c757d; color: white; padding: 0.25rem 0.75rem; border-radius: 4px; font-size: 0.8rem;">
                                            {{ $branch->name }}
                                        </span>
                                    @endif
                                </div>
                                <div style="margin-top: 0.5rem;">
                                    @include('components.service-price', ['pricing' => $pricing, 'layout' => 'compact'])
                                    <small style="color: var(--light-text); display: block;">per service</small>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="cart-item-actions">
                                <div class="cart-item-actions-row">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="quantity-form">
                                        @csrf
                                        @method('PATCH')
                                        <label for="quantity-{{ $item->id }}" style="font-size: 0.9rem; color: var(--light-text); margin-right: 0.5rem;">Qty:</label>
                                        <input type="number" name="quantity" id="quantity-{{ $item->id }}" value="{{ $item->quantity }}" min="1" max="10" 
                                               class="quantity-input"
                                               style="width: 60px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
                                    </form>
                                    
                                    <span class="item-total" style="font-weight: bold; color: var(--primary-color); min-width: 80px; text-align: right;">
                                        ₱{{ number_format($pricing['display_price'] * $item->quantity, 2) }}
                                    </span>
                                </div>
                                
                                <form method="POST" action="{{ route('cart.remove', $item) }}" class="remove-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; width: 100%;">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    
                    @if(!$loop->last)
                        <div style="margin: 1rem 0; border-top: 2px dashed #e9ecef;"></div>
                    @endif
                @endforeach
                
                <div class="cart-actions">
                    <div class="cart-actions-buttons">
                        <button type="button" onclick="selectAll()" style="background: #6c757d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">
                            Select All
                        </button>
                        <button type="button" onclick="deselectAll()" style="background: #6c757d; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;">
                            Deselect All
                        </button>
                    </div>
                    <form method="POST" action="{{ route('cart.clear') }}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background:#F0F0F0; color: #197A8C; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer;">
                            Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card order-summary" style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #e9ecef); color: var(--dark-text, #2c3e50);">
                <h2 style="color: var(--primary-color); margin-bottom: 1.5rem; font-size: 1.5rem;">Order Summary</h2>
                
                <div id="selected-items-summary" style="margin-bottom: 1.5rem; max-height: 300px; overflow-y: auto;">
                    <!-- Will be populated by JavaScript -->
                </div>
                
                <div style="border-top: 2px solid var(--primary-color); padding-top: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold;">
                        <span>Total:</span>
                        <span id="selected-total" style="color: var(--primary-color);">₱{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <button type="button" id="checkout-btn" onclick="checkoutSelected()" class="btn btn-primary" style="width: 100%; text-align: center; padding: 0.75rem; cursor: pointer;">
                        Checkout Selected Items
                    </button>
                    <a href="{{ route('services.index') }}" class="btn btn-accent" style="text-align: center; padding: 0.75rem;">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    updateTotals();
    attachQuantityListeners();
    attachRemoveListeners();
    
    // Add event listeners to all checkboxes
    document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateTotals);
    });
    
    // Checkout button is handled by checkoutSelected() function
});

function attachQuantityListeners() {
    document.querySelectorAll('.quantity-form').forEach(form => {
        form.addEventListener('submit', event => event.preventDefault());
    });

    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => handleQuantityChange(input));
    });
}

function attachRemoveListeners() {
    document.querySelectorAll('.remove-form').forEach(form => {
        form.addEventListener('submit', event => {
            event.preventDefault();
            handleRemove(form);
        });
    });
}

function formatCurrency(value) {
    return '' + Number(value).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

async function handleQuantityChange(input) {
    const form = input.closest('form');
    const cartItem = input.closest('.cart-item');
    const itemTotalEl = cartItem.querySelector('.item-total');
    const price = parseFloat(cartItem.dataset.price);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    // Clamp value between min and max
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || 10;
    let newQuantity = parseInt(input.value);
    if (isNaN(newQuantity)) newQuantity = min;
    newQuantity = Math.max(min, Math.min(max, newQuantity));
    input.value = newQuantity;

    try {
        const response = await fetch(form.action, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ quantity: newQuantity })
        });

        if (!response.ok) {
            throw new Error('Failed to update cart item.');
        }

        const data = await response.json();
        const updatedQuantity = data.quantity ?? newQuantity;
        const itemTotal = data.item_total ?? price * updatedQuantity;

        // Update UI without reloading
        cartItem.dataset.quantity = updatedQuantity;
        itemTotalEl.textContent = formatCurrency(itemTotal);
        updateTotals();
    } catch (error) {
        alert('Unable to update quantity. Please try again.');
        // Reset to previous value stored on the element
        input.value = cartItem.dataset.quantity;
    }
}

async function handleRemove(form) {
    const cartItem = form.closest('.cart-item');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    try {
        const response = await fetch(form.action, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to remove item.');
        }

        const data = await response.json();

        // Remove the item row from DOM
        cartItem.remove();

        // If no items left, reload to show empty state
        if (document.querySelectorAll('.cart-item').length === 0) {
            window.location.reload();
            return;
        }

        updateTotals();

        // Refresh cart badge if helper is available
        if (typeof updateCartBadge === 'function') {
            if (data?.count !== undefined) {
                updateCartBadge(data.count);
            } else {
                updateCartBadge();
            }
        }
    } catch (error) {
        alert('Unable to remove item. Please try again.');
    }
}

function getSelectedItems() {
    const selected = [];
    document.querySelectorAll('.cart-item-checkbox:checked').forEach(checkbox => {
        selected.push(parseInt(checkbox.dataset.cartId));
    });
    return selected;
}

function updateTotals() {
    const selectedItems = getSelectedItems();
    let total = 0;
    const summaryDiv = document.getElementById('selected-items-summary');
    const totalSpan = document.getElementById('selected-total');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    // Clear summary
    summaryDiv.innerHTML = '';
    
    if (selectedItems.length === 0) {
        summaryDiv.innerHTML = '<p style="color: var(--light-text); text-align: center; padding: 1rem;">No items selected</p>';
        totalSpan.textContent = '₱0.00';
        if (checkoutBtn) {
            checkoutBtn.disabled = true;
            checkoutBtn.style.opacity = '0.5';
            checkoutBtn.style.cursor = 'not-allowed';
        }
        return;
    }
    
    // Enable checkout button
    if (checkoutBtn) {
        checkoutBtn.disabled = false;
        checkoutBtn.style.opacity = '1';
        checkoutBtn.style.cursor = 'pointer';
    }
    
    // Calculate total and build summary
    document.querySelectorAll('.cart-item').forEach(item => {
        const cartId = parseInt(item.dataset.cartId);
        if (selectedItems.includes(cartId)) {
            const price = parseFloat(item.dataset.price);
            const quantity = parseInt(item.dataset.quantity);
            const itemTotal = price * quantity;
            total += itemTotal;
            
            const serviceName = item.querySelector('h3').textContent;
            const summaryItem = document.createElement('div');
            summaryItem.style.cssText = 'display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid var(--border-color, #e9ecef);';
            summaryItem.innerHTML = `
                <span style="font-size: 0.9rem;">${serviceName} x${quantity}</span>
                <span style="font-weight: 600;">₱${itemTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</span>
            `;
            summaryDiv.appendChild(summaryItem);
        }
    });
    
    // Update total
    totalSpan.textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function selectAll() {
    document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateTotals();
}

function deselectAll() {
    document.querySelectorAll('.cart-item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateTotals();
}

function checkoutSelected() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        alert('Please select at least one item to checkout.');
        return;
    }
    
    // Store selected items in sessionStorage
    sessionStorage.setItem('selectedCartItems', JSON.stringify(selectedItems));
    
    // Redirect to checkout with selected items as query parameter
    const baseUrl = '{{ route("consultations.create") }}';
    const params = new URLSearchParams();
    selectedItems.forEach(id => {
        params.append('cart_items[]', id);
    });
    
    window.location.href = baseUrl + '?' + params.toString();
}

</script>
@endpush
@endsection