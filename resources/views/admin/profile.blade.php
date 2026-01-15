@extends('layouts.dashboard')
@section('page-title', 'Profile')

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container">
    <h1 style="color: var(--primary-color); margin-bottom: 1.5rem;">Your Information</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" id="admin-profile-form">
            @csrf
            @method('PUT')
            
            <!-- Profile Photo -->
            <div class="form-group" style="text-align: center; margin-bottom: 2rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--primary-color);">Profile Photo</label>
                    <div style="position: relative; display: inline-block;">
                        <img id="profile-photo-preview" 
                             src="{{ $admin->profile_photo ? asset('storage/' . $admin->profile_photo) : asset('images/doctor-placeholder.jpg') }}" 
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
                <input class="form-control" type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required />
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input class="form-control" type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required />
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Branch Assigned (Read-only) -->
            <div class="form-group">
                <label for="branch">Branch Assigned</label>
                <input class="form-control" type="text" value="{{ $admin->branch ? $admin->branch->name : 'Not assigned' }}" readonly style="background-color: #f5f5f5; cursor: not-allowed;" />
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
        <form method="POST" action="{{ route('admin.profile.update-password') }}" id="change-password-form">
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
document.getElementById('admin-profile-form').addEventListener('submit', function(e) {
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


