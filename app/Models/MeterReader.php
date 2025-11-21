<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReader extends Model
{
    protected $table = 'meter_readers';
    protected $primaryKey = 'mr_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'mr_name',
        'stat_id'
    ];

    /**
     * Get the status associated with the meter reader
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the area assignments for the meter reader
     */
    public function areaAssignments()
    {
        return $this->hasMany(AreaAssignment::class, 'meter_reader_id', 'mr_id');
    }

    /**
     * Get the meter readings for the meter reader
     */
    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'meter_reader_id', 'mr_id');
    }
}