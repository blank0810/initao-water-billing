<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterBill extends Model
{
    protected $table = 'water_bill';

    protected $primaryKey = 'wb_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'cl_id',
        'per_id',
        'create_date',
        'amount',
        'stat_id',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the consumer ledger that owns the water bill
     */
    public function consumerLedger()
    {
        return $this->belongsTo(ConsumerLedger::class, 'cl_id', 'cl_id');
    }

    /**
     * Get the period that owns the water bill
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'per_id', 'per_id');
    }

    /**
     * Get the status associated with the water bill
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the meter readings for the water bill
     */
    public function meterReadings()
    {
        return $this->hasMany(MeterReadingOld::class, 'wb_id', 'wb_id');
    }
}
