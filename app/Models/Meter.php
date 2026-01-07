<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    protected $table = 'meter';

    protected $primaryKey = 'mtr_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'mtr_serial',
        'mtr_brand',
        'stat_id',
    ];

    /**
     * Get the status associated with the meter
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the meter assignments for the meter
     */
    public function meterAssignments()
    {
        return $this->hasMany(MeterAssignment::class, 'meter_id', 'mtr_id');
    }
}
