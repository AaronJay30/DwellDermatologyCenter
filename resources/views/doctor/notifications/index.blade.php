@extends('layouts.dashboard')
@section('page-title', 'Notifications')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/notification.css') }}">
@endpush

@section('navbar-links')
    @include('partials.doctor_nav')
@endsection

@section('content')
<div class="container notification-wrapper">
    <!-- Header -->
    <div class="notif-header">
        <h1>Notifications</h1>
        <form method="POST" action="{{ route('doctor.notifications.mark-all-read') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-accent">Mark All as Read</button>
        </form>
    </div>

    @if($notifications->isEmpty())
        <div class="notif-empty" id="notifications-empty">
            <h3>No notifications</h3>
            <p>You're all caught up!</p>
        </div>
    @else
        <div class="notif-group">
            <div class="notif-card" id="notifications-container">
                @foreach($notifications as $notification)
                <div class="notif-item {{ !$notification->is_read ? 'unread' : '' }}" data-notification-id="{{ $notification->id }}">
                    <div class="notif-icon">🔔</div>
                    <div class="notif-content">
                        <h3>{{ $notification->title }}</h3>
                        <p>{{ $notification->message }}</p>
                        <small>{{ $notification->created_at->format('l, g:i A') }}</small>
                    </div>
                    <div class="notif-actions">
                        @if(!$notification->is_read)
                            <form method="POST" action="{{ route('doctor.notifications.mark-read', $notification) }}" style="display: inline;" class="mark-read-form">
                                @csrf
                                <button type="submit" class="btn btn-mark">Mark as Read</button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    @endif
</div>

@push('scripts')
<script>
(function() {
    // Function to create notification HTML
    function createNotificationHTML(notification) {
        const unreadClass = !notification.is_read ? 'unread' : '';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const markReadUrl = `{{ route('doctor.notifications.mark-read', ':id') }}`.replace(':id', notification.id);
        const markReadButton = !notification.is_read 
            ? `<form method="POST" action="${markReadUrl}" style="display: inline;" class="mark-read-form">
                <input type="hidden" name="_token" value="${csrfToken}">
                <button type="submit" class="btn btn-mark">Mark as Read</button>
               </form>`
            : '';

        return `
            <div class="notif-item ${unreadClass}" data-notification-id="${notification.id}">
                <div class="notif-icon">🔔</div>
                <div class="notif-content">
                    <h3>${escapeHtml(notification.title)}</h3>
                    <p>${escapeHtml(notification.message)}</p>
                    <small>${notification.created_at}</small>
                </div>
                <div class="notif-actions">
                    ${markReadButton}
                </div>
            </div>
        `;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Function to add a single notification to the page
    function addNotification(notification) {
        const emptyDiv = document.getElementById('notifications-empty');
        
        // Remove empty message if it exists
        if (emptyDiv) {
            emptyDiv.remove();
        }

        // Create container if it doesn't exist
        let container = document.getElementById('notifications-container');
        if (!container) {
            const notifGroup = document.querySelector('.notif-group') || createNotificationGroup();
            const newContainer = document.createElement('div');
            newContainer.className = 'notif-card';
            newContainer.id = 'notifications-container';
            notifGroup.appendChild(newContainer);
            container = newContainer;
        }

        // Check if notification already exists
        const existing = container.querySelector(`[data-notification-id="${notification.id}"]`);
        if (!existing) {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = createNotificationHTML(notification);
            const newNotification = tempDiv.firstElementChild;
            
            // Insert at the top
            container.insertBefore(newNotification, container.firstChild);
            
            // Add animation
            newNotification.style.opacity = '0';
            newNotification.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                newNotification.style.transition = 'all 0.3s ease';
                newNotification.style.opacity = '1';
                newNotification.style.transform = 'translateY(0)';
            }, 10);

            // Attach event listener for mark as read
            const markReadForm = newNotification.querySelector('.mark-read-form');
            if (markReadForm) {
                markReadForm.addEventListener('submit', handleMarkAsRead);
            }

            // Show a subtle notification sound/visual feedback
            showNotificationFeedback(notification);
        }
    }

    // Show visual feedback when a new notification arrives
    function showNotificationFeedback(notification) {
        // You can add a toast notification or sound here
        console.log('New notification received:', notification.title);
        
        // Optional: Show browser notification if permission granted
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.message,
                icon: '/favicon.ico'
            });
        }
    }

    // Create notification group if it doesn't exist
    function createNotificationGroup() {
        const wrapper = document.querySelector('.notification-wrapper');
        const group = document.createElement('div');
        group.className = 'notif-group';
        wrapper.appendChild(group);
        return group;
    }

    // Handle mark as read form submission
    function handleMarkAsRead(e) {
        e.preventDefault();
        const form = e.target;
        const notificationItem = form.closest('.notif-item');
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (response.ok) {
                notificationItem.classList.remove('unread');
                form.remove();
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }

    // Attach event listeners to existing mark as read forms
    document.querySelectorAll('.mark-read-form').forEach(form => {
        form.addEventListener('submit', handleMarkAsRead);
    });

    // Request browser notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Set up real-time notifications with Laravel Echo
    document.addEventListener('DOMContentLoaded', function() {
        // Check if Echo is available
        if (typeof window.Echo !== 'undefined') {
            const userId = {{ Auth::id() }};
            
            // Listen for user-specific notifications
            window.Echo.private(`user.${userId}`)
                .listen('.notification.created', (data) => {
                    console.log('Real-time notification received:', data);
                    addNotification(data);
                });

            // Listen for global notifications
            window.Echo.channel('notifications')
                .listen('.notification.created', (data) => {
                    console.log('Global notification received:', data);
                    addNotification(data);
                });

            console.log('Real-time notifications enabled');
        } else {
            console.warn('Laravel Echo is not available. Falling back to polling.');
            // Fallback to polling if Echo is not available
            setupPollingFallback();
        }
    });

    // Fallback polling mechanism if Echo is not available
    function setupPollingFallback() {
        let lastCheckTime = '{{ $notifications->isNotEmpty() ? $notifications->first()->created_at->toDateTimeString() : now()->toDateTimeString() }}';
        let pollInterval;

        function pollForNotifications() {
            fetch(`{{ route('doctor.notifications.new') }}?last_check=${encodeURIComponent(lastCheckTime)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        addNotification(notification);
                    });
                }
                if (data.timestamp) {
                    lastCheckTime = data.timestamp;
                }
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
            });
        }

        // Poll every 5 seconds as fallback
        pollInterval = setInterval(pollForNotifications, 5000);
        setTimeout(pollForNotifications, 1000);

        // Stop polling when page is hidden, resume when visible
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(pollInterval);
            } else {
                pollInterval = setInterval(pollForNotifications, 5000);
                pollForNotifications();
            }
        });
    }
})();
</script>
@endpush
@endsection

