@extends('layouts.dashboard')
@section('page-title', 'Edit Branch')

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/form-inputs.css') }}">
@endpush

@section('content')
<style>
    .branch-form-container {
        background: rgba(255, 250, 240, 0.75) !important;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.15),
            0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .branch-form-container:hover {
        box-shadow: 
            0 8px 24px rgba(0, 0, 0, 0.12),
            0 4px 12px rgba(255, 215, 0, 0.25),
            0 2px 6px rgba(0, 0, 0, 0.15);
        background: rgba(255, 252, 248, 0.85) !important;
    }

    .profile-upload-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        padding: 0.5rem 0;
    }

    .profile-image-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin-bottom: 0.5rem;
    }

    .profile-image-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 215, 0, 0.3);
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.2);
        display: none;
    }

    .profile-image-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 215, 0, 0.2) 0%, rgba(255, 215, 0, 0.1) 100%);
        border: 3px solid rgba(255, 215, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 2rem;
        box-shadow: 
            0 4px 12px rgba(0, 0, 0, 0.08),
            0 2px 6px rgba(255, 215, 0, 0.2);
    }

    .profile-upload-label {
        cursor: pointer;
        padding: 0.6rem 1.2rem;
        background: rgba(255, 215, 0, 0.15);
        border: 2px solid rgba(255, 215, 0, 0.4);
        border-radius: 8px;
        color: #2c3e50;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
        font-size: 0.9rem;
    }

    .profile-upload-label:hover {
        background: rgba(255, 215, 0, 0.25);
        border-color: rgba(255, 215, 0, 0.6);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(255, 215, 0, 0.2);
    }

    .profile-upload-input {
        display: none;
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

    /* Ensure form fits on screen */
    .main-content {
        padding: 1rem 0;
    }

    .container {
        padding: 0 20px;
    }
</style>

<div class="container">
    
    <div class="branch-form-container compact-form">
        <form method="POST" action="{{ route('doctor.branches.update', $branch) }}" enctype="multipart/form-data" id="branchForm">
            @csrf
            @method('PUT')
            
            <!-- Profile Photo Upload - Circular Container at Top -->
            <div class="profile-upload-container">
                <div class="profile-image-wrapper">
                    @php
                        $adminUser = $branch->users()->where('role', 'admin')->first();
                        $adminPhoto = $adminUser && $adminUser->profile_photo ? asset('storage/' . $adminUser->profile_photo) . '?t=' . time() : '#';
                    @endphp
                    <img id="adminPhotoPreviewImg" class="profile-image-preview" src="{{ $adminPhoto }}" alt="Admin Photo Preview" style="{{ $adminPhoto !== '#' ? 'display: block;' : 'display: none;' }}" />
                    <div id="profilePlaceholder" class="profile-image-placeholder" style="{{ $adminPhoto !== '#' ? 'display: none;' : 'display: flex;' }}">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <label for="admin_photo" class="profile-upload-label">
                    <i class="fas fa-camera"></i> Choose Photo
                </label>
                <input type="file" id="admin_photo" name="admin_photo" class="profile-upload-input" accept="image/*">
                @error('admin_photo')
                    <div style="color: #dc3545; font-size: 0.875rem; margin-top: 0.5rem; text-align: center;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Branch Name and Address on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="name">Branch Name</label>
                    <div class="modern-input-container">
                        <i class="fas fa-building input-icon"></i>
                        <input type="text" id="name" name="name" value="{{ old('name', $branch->name) }}" placeholder="Start typing your branch name..." required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="address">Address</label>
                    <div class="modern-input-container">
                        <i class="fas fa-map-marker-alt input-icon"></i>
                        <input type="text" id="address" name="address" value="{{ old('address', $branch->address) }}" placeholder="Start typing your address..." required>
                    </div>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Phone Number and Email Address on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="phone">Phone Number</label>
                    <div class="modern-input-container">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="Start typing your phone number..." required>
                    </div>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="email">Email Address</label>
                    <div class="modern-input-container">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="Start typing your email..." required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Password and Confirm Password on one line -->
            <div class="form-row">
                <div class="modern-input-wrapper">
                    <label for="password">Password (leave blank to keep current)</label>
                    <div class="modern-input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" class="has-right-icon" placeholder="Start typing your password..." minlength="6">
                        <i class="fas fa-eye-slash input-icon-right" id="togglePassword"></i>
                    </div>
                    <small style="color: #6c757d; font-size: 0.8rem; display: block; margin-top: 0.25rem;">Minimum 6 characters (optional)</small>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modern-input-wrapper">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="modern-input-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="has-right-icon" placeholder="Start typing your password..." minlength="6">
                        <i class="fas fa-eye-slash input-icon-right" id="togglePasswordConfirmation"></i>
                    </div>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Save Changes</button>
                <a href="{{ route('doctor.branches') }}" class="btn btn-accent" style="padding: 0.6rem 1.5rem; font-size: 0.95rem;">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminPhotoInput = document.getElementById('admin_photo');
    const adminPhotoPreview = document.getElementById('adminPhotoPreviewImg');
    const profilePlaceholder = document.getElementById('profilePlaceholder');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');
    const form = document.getElementById('branchForm');
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');

    // Store original photo source for fallback
    const originalPhotoSrc = adminPhotoPreview.src;

    // Image preview functionality
    adminPhotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                adminPhotoPreview.src = e.target.result;
                adminPhotoPreview.style.display = 'block';
                profilePlaceholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            // If no file selected, restore original photo or show placeholder
            if (originalPhotoSrc && originalPhotoSrc !== '#' && !originalPhotoSrc.includes('data:') && !originalPhotoSrc.endsWith('#')) {
                adminPhotoPreview.src = originalPhotoSrc;
                adminPhotoPreview.style.display = 'block';
                profilePlaceholder.style.display = 'none';
            } else {
                adminPhotoPreview.style.display = 'none';
                profilePlaceholder.style.display = 'flex';
            }
        }
    });

    // Password toggle functionality
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    togglePasswordConfirmation.addEventListener('click', function() {
        const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Password confirmation validation
    function validatePasswordMatch() {
        if (passwordInput.value && passwordConfirmationInput.value) {
            if (passwordInput.value !== passwordConfirmationInput.value) {
                passwordConfirmationInput.setCustomValidity('Passwords do not match');
            } else {
                passwordConfirmationInput.setCustomValidity('');
            }
        }
    }

    passwordInput.addEventListener('input', validatePasswordMatch);
    passwordConfirmationInput.addEventListener('input', validatePasswordMatch);

    // Form submission validation
    form.addEventListener('submit', function(e) {
        // Only validate passwords if they are provided
        if (passwordInput.value || passwordConfirmationInput.value) {
            if (passwordInput.value !== passwordConfirmationInput.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (passwordInput.value && passwordInput.value.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        }
    });
});
</script>
@endsection


