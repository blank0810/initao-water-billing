<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterBillAdjustment extends Model
{
    protected $table = 'water_bill_adjustments';

    protected $primaryKey = 'wba_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'c_id',
        'adjust_type',
        'old_amount',
        'new_amount',
        'remarks',
        'create_date',
        'stat_id',
    ];

    protected $casts = [
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
        'create_date' => 'datetime',
    ];

    /**
     * Get the consumer that owns the water bill adjustment
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class, 'c_id', 'c_id');
    }

    /**
     * Get the status associated with the water bill adjustment
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}
