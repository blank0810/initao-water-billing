<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReadingOld extends Model
{
    protected $table = 'meter_reading';

    protected $primaryKey = 'mrr_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'cm_id',
        'wb_id',
        'create_date',
        'read_date',
        'current_reading',
        'stat_id',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'read_date' => 'datetime',
        'current_reading' => 'decimal:5',
    ];

    /**
     * Get the consumer meter that owns the meter reading
     */
    public function consumerMeter()
    {
        return $this->belongsTo(ConsumerMeter::class, 'cm_id', 'cm_id');
    }

    /**
     * Get the water bill that owns the meter reading
     */
    public function waterBill()
    {
        return $this->belongsTo(WaterBill::class, 'wb_id', 'wb_id');
    }

    /**
     * Get the status associated with the meter reading
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}
