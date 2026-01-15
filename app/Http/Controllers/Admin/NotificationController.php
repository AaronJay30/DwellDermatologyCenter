<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id') // Global notifications
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id === Auth::id() || $notification->user_id === null) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read!');
    }
}

