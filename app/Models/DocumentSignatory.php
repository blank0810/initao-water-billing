<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSignatory extends Model
{
    protected $table = 'DocumentSignatory';

    protected $fillable = [
        'position_key',
        'position_title',
        'user_id',
        'sort_order',
    ];

    // Position key constants (admin-configurable fixed signatories)
    public const APPROVING_AUTHORITY = 'APPROVING_AUTHORITY';

    public const MEEDO_OFFICER = 'MEEDO_OFFICER';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Resolve the effective user for this signatory position.
     */
    public function resolveUser(): ?User
    {
        if ($this->user_id && $this->user) {
            return $this->user;
        }

        return null;
    }
}
