<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $table = 'MeterReading';
    protected $primaryKey = 'reading_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'assignment_id',
        'period_id',
        'reading_date',
        'reading_value',
        'is_estimated',
        'meter_reader_id',
        'created_at'
    ];

    protected $casts = [
        'reading_date' => 'date',
        'reading_value' => 'decimal:3',
        'is_estimated' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Get the meter assignment that owns the meter reading
     */
    public function meterAssignment()
    {
        return $this->belongsTo(MeterAssignment::class, 'assignment_id', 'assignment_id');
    }

    /**
     * Get the period that owns the meter reading
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the meter reader that owns the meter reading
     */
    public function meterReader()
    {
        return $this->belongsTo(MeterReader::class, 'meter_reader_id', 'mr_id');
    }

    /**
     * Get the water bill history as previous reading
     */
    public function waterBillHistoryAsPrevious()
    {
        return $this->hasMany(WaterBillHistory::class, 'prev_reading_id', 'reading_id');
    }

    /**
     * Get the water bill history as current reading
     */
    public function waterBillHistoryAsCurrent()
    {
        return $this->hasMany(WaterBillHistory::class, 'curr_reading_id', 'reading_id');
    }
}