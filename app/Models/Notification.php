<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'link',
        'source_type',
        'source_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Notification types
    public const TYPE_APPLICATION_SUBMITTED = 'application_submitted';

    public const TYPE_APPLICATION_VERIFIED = 'application_verified';

    public const TYPE_APPLICATION_PAID = 'application_paid';

    public const TYPE_APPLICATION_SCHEDULED = 'application_scheduled';

    public const TYPE_APPLICATION_CONNECTED = 'application_connected';

    public const TYPE_APPLICATION_REJECTED = 'application_rejected';

    public const TYPE_APPLICATION_CANCELLED = 'application_cancelled';

    public const TYPE_CONNECTION_SUSPENDED = 'connection_suspended';

    public const TYPE_CONNECTION_DISCONNECTED = 'connection_disconnected';

    public const TYPE_CONNECTION_RECONNECTED = 'connection_reconnected';

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): self
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }

        return $this;
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): self
    {
        $this->update(['read_at' => null]);

        return $this;
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for a specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for broadcast notifications (no specific user)
     */
    public function scopeBroadcast($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Get the source model (polymorphic)
     */
    public function getSourceAttribute()
    {
        if (! $this->source_type || ! $this->source_id) {
            return null;
        }

        $modelMap = [
            'ServiceApplication' => ServiceApplication::class,
            'ServiceConnection' => ServiceConnection::class,
            'Customer' => Customer::class,
        ];

        $modelClass = $modelMap[$this->source_type] ?? null;

        if (! $modelClass) {
            return null;
        }

        return $modelClass::find($this->source_id);
    }
}
