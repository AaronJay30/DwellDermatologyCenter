@extends('layouts.dashboard')
@section('page-title', 'Available Doctor')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@push('styles')
<style>
    .available-doctor-container {
        max-width: 720px;
        margin: 0 auto;
    }

    .available-card {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid #e9ecef;
    }

    .available-header {
        margin-bottom: 1.5rem;
    }

    .available-title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #197a8c;
        margin-bottom: 0.25rem;
    }

    .available-subtitle {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .current-photo {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .current-photo img {
        max-width: 100%;
        border-radius: 10px;
        border: 3px solid #FFD700;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
    }

    .current-photo-placeholder {
        padding: 2.5rem 1rem;
        border-radius: 10px;
        border: 2px dashed #cbd5e1;
        color: #64748b;
        font-size: 0.9rem;
        background: #f8fafc;
    }

    .form-group label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.35rem;
        display: block;
    }

    .form-text {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .submit-btn {
        margin-top: 1.5rem;
        width: 100%;
        padding: 0.9rem 1rem;
        border-radius: 8px;
        border: none;
        background: #197a8c;
        color: #ffffff;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s ease, transform 0.1s ease;
    }

    .submit-btn:hover {
        background: #1a6b7a;
        transform: translateY(-1px);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="container available-doctor-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="available-card">
        <div class="available-header">
            <div class="available-title">Available Doctor Schedule Photo</div>
            <div class="available-subtitle">
                Branch: <strong>{{ $branch->name }}</strong><br>
                Upload a single photo that shows the doctor’s available schedule. Updating the photo will replace the existing one.
            </div>
        </div>

        <div class="current-photo">
            @if($branch->available_doctor_image_path)
                <img src="{{ asset('storage/' . $branch->available_doctor_image_path) }}?t={{ time() }}" alt="Available doctor schedule photo">
            @else
                <div class="current-photo-placeholder">
                    No available doctor schedule photo has been uploaded yet for this branch.
                </div>
            @endif
        </div>

        <form action="{{ route('admin.available-doctor.update') }}" method="POST" enctype="multipart/form-data" id="available-doctor-form">
            @csrf
            <div class="form-group">
                <label for="photo">Upload Schedule Photo <span style="color:#dc2626;">(required)</span></label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/jpg,image/jpeg,image/png,image/webp" required>
                @error('photo')
                    <div class="field-error" style="color:#dc2626;font-size:0.85rem;margin-top:0.25rem;">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    Recommended: clear image of the doctor’s schedule. Only one photo is stored; uploading a new one replaces the old photo.
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submit-btn">Save Schedule Photo</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('available-doctor-form');
        const submitBtn = document.getElementById('submit-btn');

        if (!form) return;

        form.addEventListener('submit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }
        });
    });
</script>
@endpush

