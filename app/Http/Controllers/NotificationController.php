<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * View all notifications
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        $unreadCount = auth()->user()->unreadNotifications->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = auth()->user()->notifications()->findOrFail($id);

            // Mark as read
            $notification->markAsRead();

            // Log who viewed this notification
            if ($notification->data) {
                $data = is_array($notification->data) ? $notification->data : json_decode($notification->data, true);
                $viewedBy = $data['viewed_by'] ?? [];

                if (!is_array($viewedBy)) {
                    $viewedBy = [];
                }

                $currentUser = auth()->user();
                $userRoles = $currentUser->getRoleNames()->implode(', ');

                $viewerInfo = [
                    'user_id' => $currentUser->id,
                    'user_name' => $currentUser->name,
                    'user_email' => $currentUser->email,
                    'user_role' => $userRoles,
                    'viewed_at' => now()->toIso8601String()
                ];

                // Check if this user already viewed it
                $alreadyViewed = false;
                foreach ($viewedBy as $viewer) {
                    if ($viewer['user_id'] === $currentUser->id) {
                        $alreadyViewed = true;
                        break;
                    }
                }

                if (!$alreadyViewed) {
                    $viewedBy[] = $viewerInfo;
                    $data['viewed_by'] = $viewedBy;
                    $notification->data = $data;
                    $notification->save();
                }
            }

            // Return JSON for AJAX requests
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read',
                    'notification_id' => $notification->id
                ], 200);
            }

            return back()->with('success', 'Notification marked as read');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Failed to mark notification as read: ' . $e->getMessage());
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function delete($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Delete all notifications
     */
    public function deleteAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'All notifications deleted');
    }
}
