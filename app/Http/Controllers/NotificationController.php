<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        
        // Mark all unread notifications as read when the user visits the page
        $user->unreadNotifications->markAsRead();

        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Fetch unread notifications for real-time updates.
     */
    public function unread()
    {
        return response()->json([
            'unread_count' => Auth::user()->unreadNotifications->count(),
            'unread_notifications' => Auth::user()->unreadNotifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $notification = Auth::user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
