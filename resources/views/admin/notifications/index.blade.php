@extends('layouts.dashboard')
@section('page-title', 'Notifications')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/notification.css') }}">
<link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
@endpush

@section('navbar-links')
    @include('admin.partials.sidebar-links')
@endsection

@section('content')
<div class="container notification-wrapper">
    <!-- Header -->
    <div class="notif-header">
        <h1>Notifications</h1>
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-accent">Mark All as Read</button>
        </form>
    </div>

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
                            <form method="POST" action="{{ route('admin.notifications.mark-read', $notification) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-mark">Mark as Read</button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 2rem;">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

