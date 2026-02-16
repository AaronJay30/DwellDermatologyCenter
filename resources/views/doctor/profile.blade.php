@extends('layouts.dashboard')
@section('page-title', 'Profile')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">Your Information</h1>
    <div class="card">
        <form method="POST" action="{{ route('doctor.profile.update') }}" enctype="multipart/form-data" id="doctor-profile-form">
            @csrf
            @method('PUT')
            
            <!-- Profile Photo -->
            <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">Profile Photo</label>
                    <div style="position: relative; display: inline-block;">
                        <img id="profile-photo-preview" 
                             src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) . '?t=' . time() : asset('images/doctor-placeholder.jpg') }}" 
                             alt="Profile Photo" 
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-color); cursor: pointer;"
                             onclick="document.getElementById('profile_photo').click()">
                        <input type="file" 
                               id="profile_photo" 
                               name="profile_photo" 
                               accept="image/*" 
                               style="display: none;"
                               onchange="previewProfilePhoto(this)">
                    </div>
                    <p style="margin-top: 0.5rem; color: #6c757d; font-size: 0.9rem;">Click on photo to change</p>
                </div>
            </div>

            <!-- Name -->
            <div class="form-group">
                <label for="name">Name</label>
                <input class="form-control" type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required />
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required />
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Specialty -->
            <div class="form-group">
                <label for="specialty">Specialty</label>
                <input class="form-control" type="text" name="specialty" id="specialty" value="{{ old('specialty', auth()->user()->specialty ?? '') }}" />
                @error('specialty')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Contact Phone / Phone -->
            <div class="form-group">
                <label for="contact_phone">Contact Phone</label>
                <input class="form-control" type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', auth()->user()->contact_phone ?? auth()->user()->phone ?? '') }}" />
                @error('contact_phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input class="form-control" type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y-m-d') : '') }}" />
                @error('date_of_birth')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gender -->
            <div class="form-group">
                <label for="gender">Gender</label>
                <select class="form-control" name="gender" id="gender">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender', auth()->user()->gender) === 'male' ? 'selected' : '' }}>male</option>
                    <option value="female" {{ old('gender', auth()->user()->gender) === 'female' ? 'selected' : '' }}>female</option>
                    <option value="other" {{ old('gender', auth()->user()->gender) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Address -->
            <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" name="address" id="address" rows="3">{{ old('address', auth()->user()->address ?? '') }}</textarea>
                @error('address')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Change Password Button -->
            <div class="form-group" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">Change Password</label>
                <button type="button" class="btn btn-secondary" onclick="openChangePasswordModal()" style="margin-bottom: 1rem;">
                    Change Password
                </button>
            </div>

            <button class="btn btn-primary" type="submit">Save Changes</button>
        </form>
    </div>
</div>

<!-- Change Password Modal -->
<div id="change-password-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%; position: relative;">
        <button onclick="closeChangePasswordModal()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        <h3 style="color: var(--primary-color); margin-bottom: 1.5rem;">Change Password</h3>
        <form method="POST" action="{{ route('doctor.profile.update-password') }}" id="change-password-form">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input class="form-control" type="password" name="current_password" id="current_password" required />
                @error('current_password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input class="form-control" type="password" name="new_password" id="new_password" required />
                @error('new_password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" required />
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">Update Password</button>
                <button type="button" class="btn btn-secondary" onclick="closeChangePasswordModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewProfilePhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('profile-photo-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function openChangePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'flex';
}

function closeChangePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'none';
    document.getElementById('change-password-form').reset();
}

// Handle form submission with success message
document.getElementById('doctor-profile-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Profile updated successfully!');
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating your profile');
    });
});

// Handle password form submission
document.getElementById('change-password-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password updated successfully!');
            closeChangePasswordModal();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating your password');
    });
});
</script>
@endsection


