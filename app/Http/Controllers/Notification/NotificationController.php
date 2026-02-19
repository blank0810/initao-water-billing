<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Show the notifications page
     */
    public function page()
    {
        return view('notifications.index');
    }

    /**
     * Get paginated notifications for authenticated user (full page API)
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(
            Auth::id(),
            $request->input('filter'),
            $request->input('category'),
            $request->input('search'),
            (int) $request->input('per_page', 15)
        );

        // Add category color to each notification
        $notifications->getCollection()->transform(function ($notification) {
            $notification->category_color = Notification::getCategoryColor($notification->type);
            $notification->time_ago = $notification->created_at->diffForHumans();

            return $notification;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get recent notifications for dropdown (5 most recent)
     */
    public function recent(): JsonResponse
    {
        $notifications = $this->notificationService->getRecentNotifications(Auth::id(), 5);

        $notifications->transform(function ($notification) {
            $notification->category_color = Notification::getCategoryColor($notification->type);
            $notification->time_ago = $notification->created_at->diffForHumans();

            return $notification;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread count for authenticated user
     */
    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Get category counts for stats cards
     */
    public function categoryCounts(): JsonResponse
    {
        $counts = $this->notificationService->getCategoryCounts(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $counts,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(int $id): JsonResponse
    {
        $notification = $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
        ]);
    }
}
