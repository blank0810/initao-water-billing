<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanSession extends Model
{
    protected $table = 'scan_sessions';

    protected $fillable = [
        'token',
        'status',
        'scanned_data',
        'expires_at',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'scanned_data' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' && ! $this->isExpired();
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
