@extends('layouts.patient')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/notification.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
<style>
    /* Mobile responsive styles for notifications */
    @media (max-width: 640px) {
        .notification-wrapper {
            padding: 10px !important;
        }
        
        .notif-header {
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px !important;
        }
        
        .notif-header h1 {
            font-size: 1.5rem !important;
        }
        
        .notif-item {
            padding: 12px !important;
            margin-bottom: 8px !important;
        }
        
        .notif-content h3 {
            font-size: 0.95rem !important;
        }
        
        .notif-content p {
            font-size: 0.85rem !important;
        }
        
        .notif-content small {
            font-size: 0.75rem !important;
        }
    }
</style>
@endpush
@section('content')
<div class="container notification-wrapper">


    <!-- Header -->
    <div class="notif-header">
        <h1>Notifications</h1>
        <form method="POST" action="{{ route('notifications.mark-all-read') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-accent">Mark All as Read</button>
        </form>
    </div>


    @if(auth()->user()->isAdmin())
    <div class="card notif-admin-form">
        <form method="POST" action="{{ route('notifications.store') }}">
            @csrf
            <div class="form-group">
                <label for="description">New Notification (description only)</label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Type the notification description..." required>{{ old('description') }}</textarea>
            </div>
            <div class="form-group form-inline">
                <select name="type" class="form-control" style="max-width: 260px;">
                    <option value="announcement">Announcement</option>
                    <option value="promotion">Promotion</option>
                    <option value="appointment_reminder">Appointment reminder</option>
                    <option value="system">System</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-send-small">Send to all Users</button>
            </div>

            @error('description')
                <div class="alert alert-error">{{ $message }}</div>
            @enderror
        </form>
    </div>
    @endif


    @if($notifications->isEmpty())
        <div class="notif-empty">
            <h3>No notifications</h3>
            <p>You're all caught up!</p>
        </div>
    @else
        <div class="notif-group">
            <div class="notif-card">
                @foreach($notifications as $notification)
                <div class="notif-item {{ !$notification->is_read ? 'unread' : '' }}">
                    <div class="notif-icon">🔔</div>
                    <div class="notif-content">
                        <h3>{{ $notification->title }}</h3>
                        <p>{{ $notification->message }}</p>
                        <small>{{ $notification->created_at->format('l, g:i A') }}</small>
                    </div>
                    <div class="notif-actions">
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-mark">Mark as Read</button>
                            </form>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <button type="button" onclick="openEditModal({{ $notification->id }}, '{{ addslashes($notification->message) }}', '{{ $notification->type }}')" class="btn btn-accent">Edit</button>
                            <button type="button" onclick="openDeleteModal({{ $notification->id }})" class="btn btn-danger">Delete</button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Pagination -->
        @if($notifications->hasPages())
        <div class="pagination-wrapper">
            {{ $notifications->links() }}
        </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    // Auto-refresh notifications every 10 seconds when on the notifications page
    let lastNotificationCount = {{ $notifications->count() }};
    let refreshInterval;
    
    function checkForNewNotifications() {
        fetch('{{ route("notifications.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const currentCount = data.count || 0;
                
                // If there are new notifications, reload the page
                if (currentCount > 0) {
                    // Check if we're on the first page and should reload
                    const currentUrl = new URL(window.location.href);
                    const page = currentUrl.searchParams.get('page') || 1;
                    
                    // Only auto-reload if we're on the first page to avoid disrupting pagination
                    if (page == 1) {
                        // Check if there are actually new notifications by comparing timestamps
                        const lastNotificationTime = document.querySelector('.notif-item')?.dataset?.timestamp;
                        if (!lastNotificationTime) {
                            // Reload to get new notifications
                            window.location.reload();
                        }
                    }
                }
            })
            .catch(error => console.error('Error checking for new notifications:', error));
    }
    
    // Start checking for new notifications every 10 seconds
    document.addEventListener('DOMContentLoaded', function() {
        refreshInterval = setInterval(checkForNewNotifications, 10000);
    });
    
    // Stop checking when user leaves the page
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }
    });
</script>
@endpush
@endsection




