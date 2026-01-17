<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingScheduleEntry extends Model
{
    protected $table = 'reading_schedule_entries';

    protected $fillable = [
        'schedule_id',
        'connection_id',
        'sequence_order',
        'status',
    ];

    /**
     * Get the reading schedule that owns this entry.
     */
    public function schedule()
    {
        return $this->belongsTo(ReadingSchedule::class, 'schedule_id', 'schedule_id');
    }

    /**
     * Get the service connection associated with this entry.
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }
}