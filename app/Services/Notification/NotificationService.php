<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\Role;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
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
     * Role mapping: which roles receive which notification types
     */
    protected function getRolesForType(string $type): array
    {
        $cashierTypes = [
            Notification::TYPE_APPLICATION_VERIFIED,
            Notification::TYPE_PAYMENT_PROCESSED,
            Notification::TYPE_BILLS_GENERATED,
        ];

        $baseRoles = [Role::SUPER_ADMIN, Role::ADMIN];

        if (in_array($type, $cashierTypes)) {
            $baseRoles[] = Role::CASHIER;
        }

        return $baseRoles;
    }

    /**
     * Resolve recipient user IDs based on notification type
     *
     * Returns users who have the required roles, excluding the acting user
     */
    protected function resolveRecipients(string $type, ?int $excludeUserId = null): Collection
    {
        $roles = $this->getRolesForType($type);

        $query = User::whereHas('roles', function ($q) use ($roles) {
            $q->whereIn('role_name', $roles);
        })->where('stat_id', Status::getIdByDescription(Status::ACTIVE));

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->pluck('id');
    }

    /**
     * Create a notification for each recipient based on role targeting
     */
    protected function notifyByRole(
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?int $excludeUserId = null
    ): void {
        $recipientIds = $this->resolveRecipients($type, $excludeUserId);

        foreach ($recipientIds as $userId) {
            $this->create($type, $title, $message, $userId, $link, $sourceType, $sourceId);
        }
    }

    /**
     * Notify about new application submission
     */
    public function notifyApplicationSubmitted(ServiceApplication $application, ?int $actingUserId = null): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';
        $barangay = $application->address?->barangay?->b_desc ?? 'Unknown';

        $this->notifyByRole(
            Notification::TYPE_APPLICATION_SUBMITTED,
            'New Application Submitted',
            "{$customerName} submitted a service application for {$barangay}",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about application verification
     */
    public function notifyApplicationVerified(ServiceApplication $application, ?int $actingUserId = null): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->notifyByRole(
            Notification::TYPE_APPLICATION_VERIFIED,
            'Application Verified',
            "Application #{$application->application_number} for {$customerName} is verified and awaiting payment",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about application payment received
     */
    public function notifyApplicationPaid(ServiceApplication $application, string $amount, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_APPLICATION_PAID,
            'Application Payment Received',
            "Payment of {$amount} received for application #{$application->application_number}",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about connection scheduled
     */
    public function notifyApplicationScheduled(ServiceApplication $application, ?int $actingUserId = null): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';
        $scheduledDate = $application->scheduled_connection_date?->format('M d, Y') ?? 'TBD';

        $this->notifyByRole(
            Notification::TYPE_APPLICATION_SCHEDULED,
            'Connection Scheduled',
            "Application #{$application->application_number} scheduled for connection on {$scheduledDate}",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about connection completed
     */
    public function notifyApplicationConnected(ServiceApplication $application, ServiceConnection $connection, ?int $actingUserId = null): void
    {
        $customerName = $application->customer?->fullName ?? 'Unknown Customer';

        $this->notifyByRole(
            Notification::TYPE_APPLICATION_CONNECTED,
            'Connection Completed',
            "{$customerName} is now connected — Account #{$connection->account_no}",
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id,
            $actingUserId
        );
    }

    /**
     * Notify about application rejection
     */
    public function notifyApplicationRejected(ServiceApplication $application, string $reason, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_APPLICATION_REJECTED,
            'Application Rejected',
            "Application #{$application->application_number} was rejected: {$reason}",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about application cancellation
     */
    public function notifyApplicationCancelled(ServiceApplication $application, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_APPLICATION_CANCELLED,
            'Application Cancelled',
            "Application #{$application->application_number} was cancelled",
            route('service.application.show', $application->application_id),
            'ServiceApplication',
            $application->application_id,
            $actingUserId
        );
    }

    /**
     * Notify about connection suspension
     */
    public function notifyConnectionSuspended(ServiceConnection $connection, ?int $actingUserId = null): void
    {
        $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

        $this->notifyByRole(
            Notification::TYPE_CONNECTION_SUSPENDED,
            'Connection Suspended',
            "Account #{$connection->account_no} ({$customerName}) has been suspended",
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id,
            $actingUserId
        );
    }

    /**
     * Notify about connection disconnection
     */
    public function notifyConnectionDisconnected(ServiceConnection $connection, ?int $actingUserId = null): void
    {
        $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

        $this->notifyByRole(
            Notification::TYPE_CONNECTION_DISCONNECTED,
            'Connection Disconnected',
            "Account #{$connection->account_no} ({$customerName}) has been disconnected",
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id,
            $actingUserId
        );
    }

    /**
     * Notify about connection reconnection
     */
    public function notifyConnectionReconnected(ServiceConnection $connection, ?int $actingUserId = null): void
    {
        $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

        $this->notifyByRole(
            Notification::TYPE_CONNECTION_RECONNECTED,
            'Connection Reconnected',
            "Account #{$connection->account_no} ({$customerName}) has been reconnected",
            route('service.connection.show', $connection->connection_id),
            'ServiceConnection',
            $connection->connection_id,
            $actingUserId
        );
    }

    /**
     * Notify about water bill payment processed
     */
    public function notifyPaymentProcessed(string $customerName, string $amount, string $receiptNo, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_PAYMENT_PROCESSED,
            'Payment Processed',
            "Payment of {$amount} received from {$customerName} — Receipt #{$receiptNo}",
            route('payment.management'),
            null,
            null,
            $actingUserId
        );
    }

    /**
     * Notify about payment cancellation
     */
    public function notifyPaymentCancelled(string $receiptNo, string $amount, string $cancelledByName, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_PAYMENT_CANCELLED,
            'Payment Cancelled',
            "Payment #{$receiptNo} of {$amount} has been cancelled by {$cancelledByName}",
            route('payment.management'),
            null,
            null,
            $actingUserId
        );
    }

    /**
     * Notify about bills generated for a period
     */
    public function notifyBillsGenerated(int $count, string $periodName, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_BILLS_GENERATED,
            'Bills Generated',
            "{$count} water bill(s) generated for period {$periodName}",
            null,
            null,
            null,
            $actingUserId
        );
    }

    /**
     * Notify about penalties processed
     */
    public function notifyPenaltiesProcessed(int $count, ?int $actingUserId = null): void
    {
        $this->notifyByRole(
            Notification::TYPE_PENALTY_PROCESSED,
            'Penalties Applied',
            "{$count} overdue penalty/ies applied",
            null,
            null,
            null,
            $actingUserId
        );
    }

    /**
     * Notify about new user created
     */
    public function notifyUserCreated(string $username, string $roleName, ?int $actingUserId = null): void
    {
        $displayRole = ucwords(str_replace('_', ' ', $roleName));

        $this->notifyByRole(
            Notification::TYPE_USER_CREATED,
            'New User Created',
            "User {$username} ({$displayRole}) has been added to the system",
            null,
            null,
            null,
            $actingUserId
        );
    }

    /**
     * Notify about auto-created period
     */
    public function notifyPeriodAutoCreated(string $periodName): void
    {
        $this->notifyByRole(
            Notification::TYPE_PERIOD_AUTO_CREATED,
            'Period Auto-Created',
            "Period {$periodName} was automatically created with rates copied from the previous period.",
            null, null, null, null
        );
    }

    /**
     * Notify about overdue reading schedule
     */
    public function notifyScheduleOverdue(string $areaName, string $periodName, int $scheduleId): void
    {
        $this->notifyByRole(
            Notification::TYPE_SCHEDULE_OVERDUE,
            'Reading Schedule Overdue',
            "Reading schedule for {$areaName} ({$periodName}) is past its end date and needs manual review.",
            null,
            'ReadingSchedule',
            $scheduleId,
            null
        );
    }

    /**
     * Get notifications for a user with optional filtering
     */
    public function getUserNotifications(
        int $userId,
        ?string $filter = null,
        ?string $category = null,
        ?string $search = null,
        int $perPage = 15
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        $query = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        if ($category) {
            $categoryTypes = $this->getTypesForCategory($category);
            $query->whereIn('type', $categoryTypes);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get recent notifications for dropdown (not paginated)
     */
    public function getRecentNotifications(int $userId, int $limit = 5): Collection
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get notification type constants for a category
     */
    protected function getTypesForCategory(string $category): array
    {
        return match ($category) {
            'applications' => [
                Notification::TYPE_APPLICATION_SUBMITTED,
                Notification::TYPE_APPLICATION_VERIFIED,
                Notification::TYPE_APPLICATION_SCHEDULED,
                Notification::TYPE_APPLICATION_CONNECTED,
                Notification::TYPE_APPLICATION_REJECTED,
                Notification::TYPE_APPLICATION_CANCELLED,
            ],
            'payments' => [
                Notification::TYPE_APPLICATION_PAID,
                Notification::TYPE_PAYMENT_PROCESSED,
                Notification::TYPE_PAYMENT_CANCELLED,
            ],
            'connections' => [
                Notification::TYPE_CONNECTION_SUSPENDED,
                Notification::TYPE_CONNECTION_DISCONNECTED,
                Notification::TYPE_CONNECTION_RECONNECTED,
            ],
            'billing' => [
                Notification::TYPE_BILLS_GENERATED,
                Notification::TYPE_PENALTY_PROCESSED,
                Notification::TYPE_PERIOD_AUTO_CREATED,
                Notification::TYPE_SCHEDULE_OVERDUE,
            ],
            'system' => [
                Notification::TYPE_USER_CREATED,
            ],
            default => [],
        };
    }

    /**
     * Get notification counts by category for a user
     */
    public function getCategoryCounts(int $userId): array
    {
        $base = Notification::where('user_id', $userId)->unread();

        return [
            'unread' => (clone $base)->count(),
            'applications' => (clone $base)->whereIn('type', $this->getTypesForCategory('applications'))->count(),
            'payments' => (clone $base)->whereIn('type', $this->getTypesForCategory('payments'))->count(),
            'connections' => (clone $base)->whereIn('type', $this->getTypesForCategory('connections'))->count(),
        ];
    }

    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
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
        return Notification::where('user_id', $userId)
            ->unread()
            ->update(['read_at' => now()]);
    }

    /**
     * Delete old notifications (cleanup) — removes notifications older than 90 days
     */
    public function cleanupOldNotifications(int $daysOld = 90): int
    {
        return Notification::where('created_at', '<', now()->subDays($daysOld))
            ->delete();
    }
}
