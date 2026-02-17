<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSchedule extends Model
{
    protected $table = 'reading_schedule';

    protected $primaryKey = 'schedule_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'period_id',
        'area_id',
        'reader_id',
        'scheduled_start_date',
        'scheduled_end_date',
        'actual_start_date',
        'actual_end_date',
        'status',
        'notes',
        'total_meters',
        'meters_read',
        'meters_missed',
        'created_by',
        'completed_by',
        'stat_id',
    ];

    protected $casts = [
        'scheduled_start_date' => 'date',
        'scheduled_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
        'total_meters' => 'integer',
        'meters_read' => 'integer',
        'meters_missed' => 'integer',
    ];

    /**
     * Get the area associated with the reading schedule.
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'a_id');
    }

    /**
     * Get the period associated with the reading schedule.
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the reader (user) assigned to this schedule.
     */
    public function reader()
    {
        return $this->belongsTo(User::class, 'reader_id', 'id');
    }

    /**
     * Get the user who created the schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the user who completed the schedule.
     */
    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by', 'id');
    }

    /**
     * Get the status associated with the reading schedule.
     */
    public function statusRecord()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Scope for pending schedules.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in-progress schedules.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for completed schedules.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for delayed schedules.
     */
    public function scopeDelayed($query)
    {
        return $query->where('status', 'delayed');
    }

    /**
     * Scope for schedules by period.
     */
    public function scopeForPeriod($query, int $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    /**
     * Scope for schedules by area.
     */
    public function scopeForArea($query, int $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    /**
     * Scope for schedules by reader.
     */
    public function scopeForReader($query, int $readerId)
    {
        return $query->where('reader_id', $readerId);
    }

    /**
     * Check if schedule is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if schedule is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if schedule is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if schedule is delayed.
     */
    public function isDelayed(): bool
    {
        return $this->status === 'delayed';
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        if ($this->total_meters === 0) {
            return 0;
        }

        return round(($this->meters_read / $this->total_meters) * 100, 2);
    }
}
