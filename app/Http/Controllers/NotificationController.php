<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = Notification::where(function($query) {
                $query->where('user_id', Auth::id())
                      ->orWhereNull('user_id'); // Global notifications
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
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

    public function store(Request $request)
    {
        // Only allow admins to create global notifications
        if (!Auth::user() || !method_exists(Auth::user(), 'isAdmin') || !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['nullable', 'in:appointment_reminder,promotion,announcement,system'],
        ]);

        Notification::create([
            'title' => 'Announcement',
            'message' => $validated['description'],
            'type' => $validated['type'] ?? 'announcement',
            'user_id' => null, // null => visible to all users
            'is_read' => false,
        ]);

        return back()->with('success', 'Notification sent to all users.');
    }

    public function update(Request $request, Notification $notification)
    {
        if (!Auth::user() || !method_exists(Auth::user(), 'isAdmin') || !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'type' => ['nullable', 'in:appointment_reminder,promotion,announcement,system'],
        ]);

        $notification->update([
            'message' => $validated['description'],
            'type' => $validated['type'] ?? $notification->type,
        ]);

        return back()->with('success', 'Notification updated.');
    }

    public function destroy(Notification $notification)
    {
        if (!Auth::user() || !method_exists(Auth::user(), 'isAdmin') || !Auth::user()->isAdmin()) {
            return back()->with('error', 'Unauthorized');
        }

        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    }
}
