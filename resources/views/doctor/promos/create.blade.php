@extends('layouts.dashboard')
@section('page-title', 'Create Promotion')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
<style>
    .promo-form-container {
        background: rgba(255, 250, 240, 0.75) !important;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    .form-section h3 {
        color: var(--primary-color);
        margin-bottom: 1rem;
        font-size: 1.25rem;
        border-bottom: 2px solid var(--primary-color);
        padding-bottom: 0.5rem;
    }
    .service-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        border: 2px solid #e9ecef;
    }
    .service-item.selected {
        border-color: var(--primary-color);
        background: #e7f3f5;
    }
    .price-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 0.5rem;
    }
    .image-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    .image-preview-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid #ddd;
    }
    .image-preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    .remove-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Create Promotion</h1>
    
    @if($errors->any())
        <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.promos.store') }}" enctype="multipart/form-data" id="promoForm">
        @csrf

        <!-- A. Promo Details -->
        <div class="form-section">
            <h3>A. Promo Details</h3>
            
            <div class="modern-input-wrapper">
                <label for="title">Promo Title <span style="color: red;">*</span></label>
                <div class="modern-input-container">
                    <i class="fas fa-heading input-icon"></i>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Enter promo title..." required>
                </div>
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-input-wrapper" style="margin-top: 1rem;">
                <label for="description">Promo Description <span style="color: red;">*</span></label>
                <div class="modern-input-container">
                    <i class="fas fa-align-left input-icon" style="top: 0.75rem;"></i>
                    <textarea id="description" name="description" rows="5" placeholder="Enter detailed description of the promotion..." required>{{ old('description') }}</textarea>
                </div>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div class="modern-input-wrapper">
                    <label for="starts_at">Start Date & Time <span style="color: red;">*</span></label>
                    <div class="modern-input-container">
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" required>
                    </div>
                    @error('starts_at')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="ends_at">End Date & Time <span style="color: red;">*</span></label>
                    <div class="modern-input-container">
                        <i class="fas fa-calendar-times input-icon"></i>
                        <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" required>
                    </div>
                    @error('ends_at')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <!-- B. Promo Photos -->
        <div class="form-section">
            <h3>B. Promo Photos (Multiple) <span style="color: red;">*</span></h3>
            <div class="modern-input-wrapper">
                <div class="image-upload-area" id="imageUploadArea">
                    <button type="button" class="upload-button">
                        <i class="fas fa-arrow-up"></i> Upload Images
                    </button>
                    <div class="upload-text">Choose images or drag & drop here</div>
                    <div class="upload-hint">JPG, JPEG, PNG and WEBP. Multiple images allowed.</div>
                    <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple required>
                </div>
                @error('images')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                @error('images.*')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div id="promoPhotosPreview" class="image-preview-grid"></div>
            </div>
        </div>

        <!-- C. Select Services -->
        <div class="form-section">
            <h3>C. Select Services Included in the Promo <span style="color: red;">*</span></h3>
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 1rem; border-radius: 8px;">
                @foreach($services as $service)
                    <div class="service-item" data-service-id="{{ $service->id }}">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="checkbox" name="services[{{ $loop->index }}][service_id]" value="{{ $service->id }}" 
                                   class="service-checkbox" data-service-price="{{ $service->price }}" 
                                   data-service-name="{{ $service->name }}">
                            <div style="flex: 1;">
                                <strong>{{ $service->name }}</strong>
                                <div style="color: #666; font-size: 0.9rem;">{{ $service->category->name ?? 'Uncategorized' }}</div>
                                <div style="color: var(--primary-color); font-weight: bold; margin-top: 0.25rem;">
                                    Original Price: ₱{{ number_format($service->price, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="price-inputs" style="display: none; margin-top: 1rem;">
                            <div class="modern-input-wrapper">
                                <label>Promo Price (₱)</label>
                                <input type="number" name="services[{{ $loop->index }}][promo_price]" 
                                       step="0.01" min="0" placeholder="Enter promo price" 
                                       class="promo-price-input" data-original="{{ $service->price }}">
                            </div>
                            <div class="modern-input-wrapper">
                                <label>OR Discount %</label>
                                <input type="number" name="services[{{ $loop->index }}][discount_percent]" 
                                       step="0.01" min="0" max="100" placeholder="Enter discount %" 
                                       class="discount-percent-input" data-original="{{ $service->price }}">
                            </div>
                            <div style="grid-column: 1 / -1; padding: 0.5rem; background: #e7f3f5; border-radius: 4px; margin-top: 0.5rem;">
                                <small>Calculated: <span class="calculated-price">-</span></small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('services')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <!-- D. Additional Options -->
        <div class="form-section">
            <h3>D. Additional Options</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="modern-input-wrapper">
                    <label for="promo_code">Promo Code (Optional)</label>
                    <div class="modern-input-container">
                        <i class="fas fa-tag input-icon"></i>
                        <input type="text" id="promo_code" name="promo_code" value="{{ old('promo_code') }}" 
                               placeholder="Auto-generated if left empty" style="text-transform: uppercase;">
                    </div>
                    <small style="color: #666;">Leave empty to auto-generate</small>
                </div>

                <div class="modern-input-wrapper">
                    <label for="max_claims_per_patient">Max Claims Per Patient (Optional)</label>
                    <div class="modern-input-container">
                        <i class="fas fa-user-times input-icon"></i>
                        <input type="number" id="max_claims_per_patient" name="max_claims_per_patient" 
                               value="{{ old('max_claims_per_patient') }}" min="1" placeholder="e.g., 1">
                    </div>
                    <small style="color: #666;">Limit how many times a patient can use this promo</small>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem; font-size: 1.1rem; font-weight: 600;">
                <i class="fas fa-save"></i> Create Promotion
            </button>
            <a href="{{ route('doctor.promos.index') }}" class="btn" style="flex: 1; padding: 1rem; font-size: 1.1rem; text-align: center; text-decoration: none; background: #6c757d; color: white; border-radius: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('images');
    const previewContainer = document.getElementById('promoPhotosPreview');
    
    imageInput.addEventListener('change', function(e) {
        previewContainer.innerHTML = '';
        Array.from(e.target.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-preview-item';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <button type="button" class="remove-image-btn" onclick="removeImagePreview(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                previewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // Service selection and pricing
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const serviceItem = this.closest('.service-item');
            const priceInputs = serviceItem.querySelector('.price-inputs');
            
            if (this.checked) {
                serviceItem.classList.add('selected');
                priceInputs.style.display = 'grid';
            } else {
                serviceItem.classList.remove('selected');
                priceInputs.style.display = 'none';
                priceInputs.querySelector('.promo-price-input').value = '';
                priceInputs.querySelector('.discount-percent-input').value = '';
                priceInputs.querySelector('.calculated-price').textContent = '-';
            }
        });
    });

    // Price calculation
    document.querySelectorAll('.promo-price-input, .discount-percent-input').forEach(input => {
        input.addEventListener('input', function() {
            const serviceItem = this.closest('.service-item');
            const originalPrice = parseFloat(this.dataset.original);
            const promoPriceInput = serviceItem.querySelector('.promo-price-input');
            const discountInput = serviceItem.querySelector('.discount-percent-input');
            const calculatedSpan = serviceItem.querySelector('.calculated-price');
            
            if (this.classList.contains('promo-price-input') && this.value) {
                const promoPrice = parseFloat(this.value);
                const discount = ((originalPrice - promoPrice) / originalPrice) * 100;
                discountInput.value = discount.toFixed(2);
                calculatedSpan.innerHTML = `Original: ₱${originalPrice.toFixed(2)} → Promo: ₱${promoPrice.toFixed(2)} (${discount.toFixed(2)}% off)`;
            } else if (this.classList.contains('discount-percent-input') && this.value) {
                const discount = parseFloat(this.value);
                const promoPrice = originalPrice * (1 - (discount / 100));
                promoPriceInput.value = promoPrice.toFixed(2);
                calculatedSpan.innerHTML = `Original: ₱${originalPrice.toFixed(2)} → Promo: ₱${promoPrice.toFixed(2)} (${discount.toFixed(2)}% off)`;
            }
        });
    });

    // Form validation
    document.getElementById('promoForm').addEventListener('submit', function(e) {
        const checkedServices = document.querySelectorAll('.service-checkbox:checked');
        if (checkedServices.length === 0) {
            e.preventDefault();
            alert('Please select at least one service for this promotion.');
            return false;
        }
        
        // Ensure each selected service has pricing
        let allValid = true;
        checkedServices.forEach(checkbox => {
            const serviceItem = checkbox.closest('.service-item');
            const promoPrice = serviceItem.querySelector('.promo-price-input').value;
            const discount = serviceItem.querySelector('.discount-percent-input').value;
            
            if (!promoPrice && !discount) {
                allValid = false;
            }
        });
        
        if (!allValid) {
            e.preventDefault();
            alert('Please provide either promo price or discount percent for all selected services.');
            return false;
        }
    });
});

function removeImagePreview(index) {
    const dt = new DataTransfer();
    const input = document.getElementById('images');
    const files = Array.from(input.files);
    files.splice(index, 1);
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
    
    // Trigger change to update preview
    input.dispatchEvent(new Event('change'));
}
</script>
@endsection

