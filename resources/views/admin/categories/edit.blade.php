@extends('layouts.dashboard')
@section('page-title', 'Edit Category')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
@endpush

@section('content')
<style>
    .category-form-container {
        background: rgba(255, 250, 240, 0.75) !important;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow:
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.15),
            0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-form-container:hover {
        box-shadow:
            0 8px 24px rgba(0, 0, 0, 0.12),
            0 4px 12px rgba(255, 215, 0, 0.25),
            0 2px 6px rgba(0, 0, 0, 0.15);
        background: rgba(255, 252, 248, 0.85) !important;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .category-image-preview-container {
        display: none;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        margin-top: 1rem;
        border-radius: 12px;
        border: 2px dashed rgba(255, 215, 0, 0.3);
        background: rgba(255, 250, 240, 0.5);
    }

    .category-image-preview-container img {
        max-width: 240px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
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

    .field-note {
        color: #6c757d;
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: block;
    }
</style>

<div class="container">
    <div class="category-form-container compact-form">
        <div class="form-header">
            <div>
                <h1 style="color: var(--primary-color); margin: 0;">Edit Category</h1>
                <p style="margin: 0.35rem 0 0; color: #6c757d;">Update the details for this category in {{ $branch->name }}.</p>
            </div>
            <a href="{{ route('admin.categories') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem;">Back to Categories</a>
        </div>

        <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" id="adminCategoryEditForm">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="name">Category Name</label>
                    <div class="modern-input-container">
                        <i class="fas fa-tag input-icon"></i>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $category->name) }}"
                            placeholder="Category name"
                            required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="branch_display">Clinic / Branch</label>
                    <div class="modern-input-container">
                        <i class="fas fa-building input-icon"></i>
                        <input type="text" id="branch_display" value="{{ $branch->name }}" disabled>
                    </div>
                    <span class="field-note">Categories remain scoped to {{ $branch->name }}.</span>
                </div>
            </div>

            <div class="modern-input-wrapper">
                <label for="description">Description (Optional)</label>
                <div class="modern-input-container">
                    <i class="fas fa-align-left input-icon" style="top: 0.75rem;"></i>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Describe what services this category covers...">{{ old('description', $category->description) }}</textarea>
                </div>
                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="modern-input-wrapper">
                <label for="image">Category Image</label>
                <div class="image-upload-area" id="adminCategoryEditImageUploadArea">
                    <button type="button" class="upload-button">
                        <i class="fas fa-arrow-up"></i> Upload
                    </button>
                    <div class="upload-text">Choose a new image or drag & drop it here.</div>
                    <div class="upload-hint">JPG, JPEG, PNG and WEBP.</div>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
                </div>
                @error('image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
                <div id="adminCategoryEditImagePreview" class="category-image-preview-container">
                    @if($category->image_path)
                        <img id="adminCategoryEditImagePreviewImg" src="{{ asset('storage/'.$category->image_path) }}" alt="Image preview">
                    @else
                        <img id="adminCategoryEditImagePreviewImg" src="#" alt="Image preview">
                    @endif
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Save Changes</button>
                <a href="{{ route('admin.categories') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imageUploadArea = document.getElementById('adminCategoryEditImageUploadArea');
    const uploadButton = imageUploadArea ? imageUploadArea.querySelector('.upload-button') : null;
    const previewContainer = document.getElementById('adminCategoryEditImagePreview');
    const previewImg = document.getElementById('adminCategoryEditImagePreviewImg');
    const hasExistingImage = previewImg && previewImg.src && !previewImg.src.endsWith('#');

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) {
            if (!hasExistingImage) {
                previewContainer.style.display = 'none';
                previewImg.src = '#';
            }
            return;
        }

        const url = URL.createObjectURL(file);
        previewImg.src = url;
        previewContainer.style.display = 'flex';
    }

    if (previewContainer && hasExistingImage) {
        previewContainer.style.display = 'flex';
    }

    if (imageInput && uploadButton) {
        uploadButton.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            imageInput.click();
        });
    }

    if (imageInput) {
        imageInput.addEventListener('change', function(event) {
            const [file] = event.target.files;
            showPreview(file);
        });
    }

    if (imageUploadArea && imageInput) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            imageUploadArea.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });

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
            const [file] = e.dataTransfer.files;
            if (file && file.type.startsWith('image/')) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
                showPreview(file);
            }
        }, false);
    }
});
</script>
@endpush


