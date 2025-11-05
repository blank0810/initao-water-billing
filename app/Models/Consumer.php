<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    protected $table = 'Consumer';
    protected $primaryKey = 'c_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'cust_id',
        'cm_id',
        'a_id',
        'stat_id',
        'chng_mtr_stat'
    ];

    protected $casts = [
        'chng_mtr_stat' => 'boolean',
    ];

    /**
     * Get the customer that owns the consumer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }

    /**
     * Get the consumer meter that owns the consumer
     */
    public function consumerMeter()
    {
        return $this->belongsTo(ConsumerMeter::class, 'cm_id', 'cm_id');
    }

    /**
     * Get the area that owns the consumer
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'a_id', 'a_id');
    }

    /**
     * Get the status associated with the consumer
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the consumer ledger entries for the consumer
     */
    public function consumerLedgerEntries()
    {
        return $this->hasMany(ConsumerLedger::class, 'c_id', 'c_id');
    }

    /**
     * Get the water bill adjustments for the consumer
     */
    public function waterBillAdjustments()
    {
        return $this->hasMany(WaterBillAdjustment::class, 'c_id', 'c_id');
    }
}