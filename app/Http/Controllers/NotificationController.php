<?php

namespace App\Http\Controllers;

use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Get notifications for authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 50);
        $notifications = $this->notificationService->getUserNotifications(auth()->id(), $limit);

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
        $count = $this->notificationService->getUnreadCount(auth()->id());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(int $id): JsonResponse
    {
        try {
            $notification = $this->notificationService->markAsRead($id);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'data' => $notification,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(auth()->id());

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
        ]);
    }
}
