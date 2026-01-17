<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAssignment extends Model
{
    protected $table = 'AreaAssignment';

    protected $primaryKey = 'area_assignment_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'area_id',
        'user_id',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Get the area that owns the area assignment
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'a_id');
    }

    /**
     * Get the user (meter reader) that owns the area assignment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope for active assignments (no end date or end date in future)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('effective_to')
                ->orWhere('effective_to', '>=', now()->format('Y-m-d'));
        });
    }

    /**
     * Scope for assignments by user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for assignments by area
     */
    public function scopeForArea($query, int $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    /**
     * Check if assignment is currently active
     */
    public function isActive(): bool
    {
        if ($this->effective_to === null) {
            return true;
        }

        return $this->effective_to->gte(now()->startOfDay());
    }
}
