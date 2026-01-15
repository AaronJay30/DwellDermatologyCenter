@extends('layouts.dashboard')
@section('page-title', 'Edit Promotion')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
<style>
    .promo-form-container { background: rgba(255, 250, 240, 0.75) !important; border-radius: 16px; padding: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); }
    .form-section { margin-bottom: 2rem; padding: 1.5rem; background: #fff; border-radius: 12px; border: 1px solid #e9ecef; }
    .form-section h3 { color: var(--primary-color); margin-bottom: 1rem; font-size: 1.25rem; border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem; }
    .service-item { background: #f8f9fa; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border: 2px solid #e9ecef; }
    .service-item.selected { border-color: var(--primary-color); background: #e7f3f5; }
    .price-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 0.5rem; }
    .image-preview-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem; margin-top: 1rem; }
    .image-preview-item { position: relative; border-radius: 8px; overflow: hidden; border: 2px solid #ddd; }
    .image-preview-item img { width: 100%; height: 150px; object-fit: cover; }
    .remove-image-btn { position: absolute; top: 5px; right: 5px; background: #dc3545; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; }
    .existing-image { position: relative; }
</style>
@endpush

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 2rem;">Edit Promotion</h1>
    
    @if($errors->any())
        <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1.5rem; border-radius: 8px;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.promos.update', $promo) }}" enctype="multipart/form-data" id="promoForm">
        @csrf
        @method('PUT')

        <!-- A. Promo Details -->
        <div class="form-section">
            <h3>A. Promo Details</h3>
            
            <div class="modern-input-wrapper">
                <label for="title">Promo Title <span style="color: red;">*</span></label>
                <div class="modern-input-container">
                    <i class="fas fa-heading input-icon"></i>
                    <input type="text" id="title" name="title" value="{{ old('title', $promo->display_title) }}" required>
                </div>
            </div>

            <div class="modern-input-wrapper" style="margin-top: 1rem;">
                <label for="description">Promo Description <span style="color: red;">*</span></label>
                <div class="modern-input-container">
                    <i class="fas fa-align-left input-icon" style="top: 0.75rem;"></i>
                    <textarea id="description" name="description" rows="5" required>{{ old('description', $promo->description) }}</textarea>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div class="modern-input-wrapper">
                    <label for="starts_at">Start Date & Time <span style="color: red;">*</span></label>
                    <div class="modern-input-container">
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="datetime-local" id="starts_at" name="starts_at" 
                               value="{{ old('starts_at', $promo->starts_at ? $promo->starts_at->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                </div>

                <div class="modern-input-wrapper">
                    <label for="ends_at">End Date & Time <span style="color: red;">*</span></label>
                    <div class="modern-input-container">
                        <i class="fas fa-calendar-times input-icon"></i>
                        <input type="datetime-local" id="ends_at" name="ends_at" 
                               value="{{ old('ends_at', $promo->ends_at ? $promo->ends_at->format('Y-m-d\TH:i') : '') }}" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- B. Promo Photos -->
        <div class="form-section">
            <h3>B. Promo Photos</h3>
            
            @if($promo->images->count() > 0)
                <div style="margin-bottom: 1rem;">
                    <label>Existing Images:</label>
                    <div class="image-preview-grid">
                        @foreach($promo->images as $image)
                            <div class="existing-image">
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Promo Image">
                                <label style="position: absolute; top: 5px; left: 5px; background: rgba(0,0,0,0.7); color: white; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}"> Remove
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="modern-input-wrapper">
                <label>Add New Images</label>
                <div class="image-upload-area" id="imageUploadArea">
                    <button type="button" class="upload-button"><i class="fas fa-arrow-up"></i> Upload Images</button>
                    <div class="upload-text">Choose images or drag & drop here</div>
                    <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
                </div>
                <div id="promoPhotosPreview" class="image-preview-grid"></div>
            </div>
        </div>

        <!-- C. Select Services -->
        <div class="form-section">
            <h3>C. Select Services Included in the Promo <span style="color: red;">*</span></h3>
            <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 1rem; border-radius: 8px;">
                @php
                    $selectedServiceIds = $promo->promoServices->pluck('service_id')->toArray();
                @endphp
                @foreach($services as $service)
                    @php
                        $isSelected = in_array($service->id, $selectedServiceIds);
                        $promoService = $promo->promoServices->where('service_id', $service->id)->first();
                    @endphp
                    <div class="service-item {{ $isSelected ? 'selected' : '' }}" data-service-id="{{ $service->id }}">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <input type="checkbox" name="services[{{ $loop->index }}][service_id]" value="{{ $service->id }}" 
                                   class="service-checkbox" data-service-price="{{ $service->price }}" 
                                   {{ $isSelected ? 'checked' : '' }}>
                            <div style="flex: 1;">
                                <strong>{{ $service->name }}</strong>
                                <div style="color: #666; font-size: 0.9rem;">{{ $service->category->name ?? 'Uncategorized' }}</div>
                                <div style="color: var(--primary-color); font-weight: bold; margin-top: 0.25rem;">
                                    Original Price: ₱{{ number_format($service->price, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="price-inputs" style="display: {{ $isSelected ? 'grid' : 'none' }}; margin-top: 1rem;">
                            <div class="modern-input-wrapper">
                                <label>Promo Price (₱)</label>
                                <input type="number" name="services[{{ $loop->index }}][promo_price]" 
                                       step="0.01" min="0" value="{{ $promoService ? $promoService->promo_price : '' }}" 
                                       class="promo-price-input" data-original="{{ $service->price }}">
                            </div>
                            <div class="modern-input-wrapper">
                                <label>OR Discount %</label>
                                <input type="number" name="services[{{ $loop->index }}][discount_percent]" 
                                       step="0.01" min="0" max="100" value="{{ $promoService ? $promoService->discount_percent : '' }}" 
                                       class="discount-percent-input" data-original="{{ $service->price }}">
                            </div>
                            <div style="grid-column: 1 / -1; padding: 0.5rem; background: #e7f3f5; border-radius: 4px; margin-top: 0.5rem;">
                                <small>Calculated: <span class="calculated-price">
                                    @if($promoService)
                                        Original: ₱{{ number_format($service->price, 2) }} → Promo: ₱{{ number_format($promoService->promo_price, 2) }} ({{ number_format($promoService->discount_percent, 2) }}% off)
                                    @else
                                        -
                                    @endif
                                </span></small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- D. Additional Options -->
        <div class="form-section">
            <h3>D. Additional Options</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="modern-input-wrapper">
                    <label for="promo_code">Promo Code</label>
                    <div class="modern-input-container">
                        <i class="fas fa-tag input-icon"></i>
                        <input type="text" id="promo_code" name="promo_code" value="{{ old('promo_code', $promo->promo_code) }}" style="text-transform: uppercase;">
                    </div>
                </div>
                <div class="modern-input-wrapper">
                    <label for="max_claims_per_patient">Max Claims Per Patient</label>
                    <div class="modern-input-container">
                        <i class="fas fa-user-times input-icon"></i>
                        <input type="number" id="max_claims_per_patient" name="max_claims_per_patient" 
                               value="{{ old('max_claims_per_patient', $promo->max_claims_per_patient) }}" min="1">
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 1rem; font-size: 1.1rem;">
                <i class="fas fa-save"></i> Update Promotion
            </button>
            <a href="{{ route('admin.promos') }}" class="btn" style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; background: #6c757d; color: white; border-radius: 8px;">
                Cancel
            </a>
        </div>
    </form>
</div>

<script src="{{ asset('js/promo-form.js') }}"></script>
@endsection

