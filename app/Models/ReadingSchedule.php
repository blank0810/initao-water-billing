<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSchedule extends Model
{
    protected $table = 'reading_schedule';
    protected $primaryKey = 'rs_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'a_id',
        'per_id',
        'rs_start_date',
        'stat_id'
    ];

    protected $casts = [
        'rs_start_date' => 'datetime',
    ];

    /**
     * Get the area that owns the reading schedule
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'a_id', 'a_id');
    }

    /**
     * Get the period that owns the reading schedule
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'per_id', 'per_id');
    }

    /**
     * Get the status associated with the reading schedule
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}