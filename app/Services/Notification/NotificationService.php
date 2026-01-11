<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Create a notification
     */
    public function create(
        string $type,
        string $title,
        string $message,
        ?int $userId = null,
        ?string $link = null,
        ?string $sourceType = null,
        ?int $sourceId = null
    ): Notification {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);
    }

    /**
     * Notify about new application submission
     */
    public function notifyApplicationSubmitted(ServiceApplication $application): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        // Notify all users with customers.manage permission (broadcast)
        $this->create(
            Notification::TYPE_APPLICATION_SUBMITTED,
            'New Service Application',
            "New service application submitted by {$customerName}. Application #: {$application->application_number}",
            null, // Broadcast to all relevant users
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id
        );
    }

    /**
     * Notify about application verification
     */
    public function notifyApplicationVerified(ServiceApplication $application): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->create(
            Notification::TYPE_APPLICATION_VERIFIED,
            'Application Verified',
            "Application #{$application->application_number} for {$customerName} has been verified. Ready for payment.",
            null,
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id
        );
    }

    /**
     * Notify about payment received
     */
    public function notifyApplicationPaid(ServiceApplication $application): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->create(
            Notification::TYPE_APPLICATION_PAID,
            'Payment Received',
            "Payment received for application #{$application->application_number} ({$customerName}). Ready for scheduling.",
            null,
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id
        );
    }

    /**
     * Notify about connection scheduled
     */
    public function notifyApplicationScheduled(ServiceApplication $application): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';
        $scheduledDate = $application->scheduled_connection_date?->format('M d, Y') ?? 'TBD';

        $this->create(
            Notification::TYPE_APPLICATION_SCHEDULED,
            'Connection Scheduled',
            "Connection scheduled for {$customerName} (App #{$application->application_number}) on {$scheduledDate}.",
            null,
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id
        );
    }

    /**
     * Notify about connection completed
     */
    public function notifyApplicationConnected(ServiceApplication $application, ServiceConnection $connection): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->create(
            Notification::TYPE_APPLICATION_CONNECTED,
            'Service Connected',
            "Service connection completed for {$customerName}. Account #: {$connection->account_no}",
            null,
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id
        );
    }

    /**
     * Notify about application rejection
     */
    public function notifyApplicationRejected(ServiceApplication $application): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->create(
            Notification::TYPE_APPLICATION_REJECTED,
            'Application Rejected',
            "Application #{$application->application_number} for {$customerName} has been rejected.",
            null,
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id
        );
    }

    /**
     * Notify about connection suspension
     */
    public function notifyConnectionSuspended(ServiceConnection $connection): void
    {
        $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

        $this->create(
            Notification::TYPE_CONNECTION_SUSPENDED,
            'Connection Suspended',
            "Service connection {$connection->account_no} for {$customerName} has been suspended.",
            null,
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id
        );
    }

    /**
     * Get notifications for a user (including broadcasts)
     */
    public function getUserNotifications(int $userId, int $limit = 50): Collection
    {
        return Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id'); // Include broadcast notifications
        })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->unread()
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): Notification
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->markAsRead();

        return $notification;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old read notifications (cleanup)
     */
    public function cleanupOldNotifications(int $daysOld = 30): int
    {
        return Notification::read()
            ->where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
