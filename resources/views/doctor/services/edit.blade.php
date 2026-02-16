@extends('layouts.dashboard')
@section('page-title', 'Edit Service')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
@endpush

@section('content')
<style>
    .service-form-container {
        background: rgba(255, 250, 240, 0.75) !important;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.15),
            0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .service-form-container:hover {
        box-shadow: 
            0 8px 24px rgba(0, 0, 0, 0.12),
            0 4px 12px rgba(255, 215, 0, 0.25),
            0 2px 6px rgba(0, 0, 0, 0.15);
        background: rgba(255, 252, 248, 0.85) !important;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .form-actions {
        margin-top: 0.75rem;
        display: flex;
        gap: 0.75rem;
    }

    .container {
        padding: 0 20px;
    }

    .service-photos-section {
        margin-top: 0.75rem;
    }

    .service-photos-section .modern-input-wrapper {
        margin-bottom: 0.75rem;
    }

    .image-upload-area {
        border: 2px dashed #ccc;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .image-upload-area:hover {
        border-color: #999;
        background: #f9f9f9;
    }

    .image-upload-area.dragover {
        border-color: #007bff;
        background: #f0f8ff;
    }

    .upload-button {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .upload-button:hover {
        background: #f5f5f5;
        border-color: #999;
    }

    .upload-button i {
        font-size: 0.8rem;
    }

    .upload-text {
        color: #666;
        font-size: 0.9rem;
        margin: 0.5rem 0;
    }

    .upload-hint {
        color: #999;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .image-upload-area input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .service-photos-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1rem;
        padding: 0.75rem;
        background: rgba(255, 250, 240, 0.5);
        border-radius: 12px;
        border: 2px dashed rgba(255, 215, 0, 0.3);
        min-height: 80px;
        align-items: center;
        justify-content: flex-start;
    }

    .service-photos-preview-container:empty {
        display: none;
    }

    .service-photo-preview-item {
        position: relative;
        min-width: 100px;
        min-height: 100px;
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid rgba(255, 215, 0, 0.4);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
    }

    .service-photo-preview-item img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }

    .service-photo-preview-item .remove-photo {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .service-photo-preview-item .remove-photo:hover {
        background: #c82333;
    }

    .existing-photos-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 0.5rem;
        padding: 0.75rem;
        background: rgba(255, 250, 240, 0.5);
        border-radius: 12px;
        border: 2px dashed rgba(255, 215, 0, 0.3);
    }

    .existing-photo-item {
        position: relative;
        min-width: 100px;
        min-height: 100px;
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid rgba(255, 215, 0, 0.4);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
    }

    .existing-photo-item img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        display: block;
    }

    .existing-photo-item .remove-existing-photo {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.7rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .existing-photo-item .remove-existing-photo:hover {
        background: #c82333;
    }

    .existing-photo-item input[type="checkbox"] {
        position: absolute;
        bottom: 5px;
        left: 5px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>

<div class="container">
    <div class="service-form-container compact-form">
        <form method="POST" action="{{ route('doctor.services.update', $service) }}" enctype="multipart/form-data" id="serviceForm">
            @csrf
            @method('PUT')
            
            <!-- Existing Service Photos -->
            @if($service->images && $service->images->count() > 0)
            <div class="service-photos-section">
                <div class="modern-input-wrapper">
                    <label style="font-weight: bold; margin-bottom: 0.5rem; display: block;">Existing Images</label>
                    <div class="existing-photos-container">
                        @foreach($service->images as $img)
                            <div class="existing-photo-item" data-image-id="{{ $img->id }}">
                                <img src="{{ asset('storage/'.$img->image_path) }}" alt="Existing photo" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div style=\'width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#f5f5f5; border:2px dashed #ccc; border-radius:8px; color:#999; font-size:0.75rem; text-align:center; padding:0.5rem;\'>Image<br/>Not Found</div>';">
                                <input type="checkbox" name="remove_image_ids[]" value="{{ $img->id }}" class="remove-checkbox" style="display: none;">
                                <div class="remove-existing-photo" onclick="toggleRemoveExisting(this)">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Service Photos -->
            <div class="service-photos-section">
                <div class="modern-input-wrapper">
                    <label style="font-weight: bold; margin-bottom: 0.5rem; display: block;">Add New Images</label>
                    <div class="image-upload-area" id="imageUploadArea">
                        <button type="button" class="upload-button">
                            <i class="fas fa-arrow-up"></i> Upload
                        </button>
                        <div class="upload-text">Choose images or drag & drop it here.</div>
                        <div class="upload-hint">JPG, JPEG, PNG and WEBP.</div>
                        <input type="file" id="images" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple>
                    </div>
                    @error('images')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('images.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <!-- Preview container for multiple photos directly below upload area -->
                    <div id="servicePhotosPreview" class="service-photos-preview-container" style="display: none;"></div>
                </div>
            </div>

            <!-- Branch and Category on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="branch_id">Clinic / Branch</label>
                    <div class="modern-input-container">
                        <i class="fas fa-building input-icon"></i>
                        <select id="branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            @isset($branches)
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ (string)($selectedBranchId ?? request('branch_id')) === (string)$branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    @error('branch_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="category_id">Category</label>
                    <div class="modern-input-container">
                        <i class="fas fa-tags input-icon"></i>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('category_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @if(!($selectedBranchId ?? request('branch_id')))
                        <small style="color: #6c757d; font-size: 0.8rem; display: block; margin-top: 0.25rem;">Tip: Select a branch first to limit categories.</small>
                    @endif
                </div>
            </div>

            <!-- Service Name and Price on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="name">Service Name</label>
                    <div class="modern-input-container">
                        <i class="fas fa-stethoscope input-icon"></i>
                        <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" placeholder="Start typing service name..." required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="price">Price (₱)</label>
                    <div class="modern-input-container">
                        <i class="fas fa-peso-sign input-icon"></i>
                        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $service->price) }}" placeholder="0.00" required>
                    </div>
                    @error('price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Duration and Active Status on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="duration_minutes">Duration (minutes)</label>
                    <div class="modern-input-container">
                        <i class="fas fa-clock input-icon"></i>
                        <input type="number" id="duration_minutes" name="duration_minutes" step="1" min="1" value="{{ old('duration_minutes', $service->duration_minutes ?? 30) }}" placeholder="30" required>
                    </div>
                    <div id="duration-display" style="margin-top: 0.25rem; font-size: 0.85rem; color: #6c757d; font-weight: 500;"></div>
                    @error('duration_minutes')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

            
            </div>

            <!-- Description - Full Width -->
            <div class="modern-input-wrapper">
                <label for="description">Description (Optional)</label>
                <div class="modern-input-container">
                    <i class="fas fa-align-left input-icon" style="top: 0.75rem;"></i>
                    <textarea id="description" name="description" rows="4" placeholder="Enter service description...">{{ old('description', $service->description) }}</textarea>
                </div>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Update Service</button>
                <a href="{{ route('doctor.services') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Multiple photos preview functionality
    const imagesInput = document.getElementById('images');
    const photosPreviewContainer = document.getElementById('servicePhotosPreview');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const uploadButton = imageUploadArea ? imageUploadArea.querySelector('.upload-button') : null;
    let selectedFiles = [];

    // Handle branch change without page refresh
    const branchSelect = document.getElementById('branch_id');
    const categorySelect = document.getElementById('category_id');
    
    if (branchSelect && categorySelect) {
        branchSelect.addEventListener('change', function() {
            const branchId = this.value;
            const currentCategoryId = categorySelect.value;
            
            // Show loading state
            categorySelect.disabled = true;
            categorySelect.innerHTML = '<option value="">Loading categories...</option>';
            
            // Fetch categories (use 0 for all categories when no branch selected)
            const fetchBranchId = branchId || '0';
            fetch(`/api/branches/${fetchBranchId}/categories`)
                .then(response => response.json())
                .then(data => {
                    // Clear and populate category dropdown
                    categorySelect.innerHTML = '<option value="">Select a category</option>';
                    
                    if (data.success && data.categories && data.categories.length > 0) {
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            // Preserve previously selected category if it still exists
                            if (currentCategoryId && currentCategoryId == category.id) {
                                option.selected = true;
                            }
                            categorySelect.appendChild(option);
                        });
                    } else {
                        categorySelect.innerHTML = '<option value="">No categories available</option>';
                    }
                    
                    categorySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                    categorySelect.innerHTML = '<option value="">Error loading categories</option>';
                    categorySelect.disabled = false;
                });
        });
    }

    // Make upload button trigger file input
    if (uploadButton && imagesInput) {
        uploadButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            imagesInput.click();
        });
    }

    // Drag and drop functionality
    if (imageUploadArea && imagesInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, function() {
                imageUploadArea.classList.add('dragover');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, function() {
                imageUploadArea.classList.remove('dragover');
            }, false);
        });

        imageUploadArea.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const dataTransfer = new DataTransfer();
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        dataTransfer.items.add(file);
                    }
                });
                imagesInput.files = dataTransfer.files;
                imagesInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }, false);
    }

    if (imagesInput) {
        imagesInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            
            // Add new files to existing selectedFiles array
            newFiles.forEach(file => {
                if (file.type.startsWith('image/')) {
                    // Check if file is not already in selectedFiles
                    const isDuplicate = selectedFiles.some(existingFile => 
                        existingFile.name === file.name && 
                        existingFile.size === file.size && 
                        existingFile.lastModified === file.lastModified
                    );
                    
                    if (!isDuplicate) {
                        selectedFiles.push(file);
                    }
                }
            });
            
            // Update the input files
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => {
                dataTransfer.items.add(file);
            });
            imagesInput.files = dataTransfer.files;
            
            // Update preview display
            updatePreview();
        });
    }

    function updatePreview() {
        // Clear previous previews
        photosPreviewContainer.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            photosPreviewContainer.style.display = 'flex';

            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'service-photo-preview-item';
                        previewItem.dataset.index = index;
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Preview ' + (index + 1);
                        
                        // Set container size based on image aspect ratio
                        img.onload = function() {
                            const aspectRatio = img.naturalWidth / img.naturalHeight;
                            if (aspectRatio > 1) {
                                // Landscape: wider than tall
                                previewItem.style.width = '180px';
                                previewItem.style.height = (180 / aspectRatio) + 'px';
                            } else if (aspectRatio < 1) {
                                // Portrait: taller than wide
                                previewItem.style.width = (120 * aspectRatio) + 'px';
                                previewItem.style.height = '120px';
                            } else {
                                // Square
                                previewItem.style.width = '120px';
                                previewItem.style.height = '120px';
                            }
                        };
                        
                        const removeBtn = document.createElement('div');
                        removeBtn.className = 'remove-photo';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                        removeBtn.onclick = function() {
                            removePhoto(index);
                        };
                        
                        previewItem.appendChild(img);
                        previewItem.appendChild(removeBtn);
                        photosPreviewContainer.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            photosPreviewContainer.style.display = 'none';
        }
    }

    function removePhoto(index) {
        // Remove from selectedFiles array
        selectedFiles.splice(index, 1);
        
        // Create new FileList
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        // Update input files
        imagesInput.files = dataTransfer.files;
        
        // Update preview display
        updatePreview();
    }

    // Function to toggle remove checkbox for existing photos
    window.toggleRemoveExisting = function(button) {
        const photoItem = button.closest('.existing-photo-item');
        const checkbox = photoItem.querySelector('.remove-checkbox');
        const isChecked = checkbox.checked;
        
        checkbox.checked = !isChecked;
        
        if (checkbox.checked) {
            photoItem.style.opacity = '0.5';
            photoItem.style.borderColor = '#dc3545';
        } else {
            photoItem.style.opacity = '1';
            photoItem.style.borderColor = 'rgba(255, 215, 0, 0.4)';
        }
    };

    // Duration display formatter
    const durationInput = document.getElementById('duration_minutes');
    const durationDisplay = document.getElementById('duration-display');
    
    function formatDuration(minutes) {
        if (!minutes || minutes < 1) {
            return '';
        }
        
        const hours = Math.floor(minutes / 60);
        const mins = minutes % 60;
        
        if (hours > 0 && mins > 0) {
            return `${hours}hr${hours > 1 ? 's' : ''} and ${mins} minute${mins > 1 ? 's' : ''}`;
        } else if (hours > 0) {
            return `${hours}hr${hours > 1 ? 's' : ''}`;
        } else {
            return `${mins} minute${mins > 1 ? 's' : ''}`;
        }
    }
    
    function updateDurationDisplay() {
        const minutes = parseInt(durationInput.value);
        if (minutes && minutes >= 1) {
            durationDisplay.textContent = formatDuration(minutes);
        } else {
            durationDisplay.textContent = '';
        }
    }
    
    if (durationInput && durationDisplay) {
        // Update on input change
        durationInput.addEventListener('input', updateDurationDisplay);
        // Initial display
        updateDurationDisplay();
    }
});
</script>
@endsection
