<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:doctor');
    }

    public function index()
    {
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id'); // Global notifications
            })
            ->latest()
            ->get();

        return view('doctor.notifications.index', compact('notifications'));
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
        Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read!');
    }

    public function getNewNotifications(Request $request)
    {
        $lastCheck = $request->input('last_check', now()->subDays(1)->toDateTimeString());
        
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('created_at', '>', $lastCheck)
            ->latest()
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->format('l, g:i A'),
                    'created_at_raw' => $notification->created_at->toDateTimeString(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public function unreadCount()
    {
        $count = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id');
            })
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}

