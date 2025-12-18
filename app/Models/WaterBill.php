<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterBill extends Model
{
    protected $table = 'water_bills';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'connection_id',
        'billing_period_id',
        'previous_reading_id',
        'current_reading_id',
        'consumption',
        'amount',
        'due_date',
        'adjustment_amount',
        'status_id'
    ];

    protected $casts = [
        'consumption' => 'decimal:3',
        'amount' => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'total_amount' => 'decimal:2', // Generated column
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function connection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id');
    }

    public function billingPeriod()
    {
        return $this->belongsTo(BillingPeriod::class);
    }

    public function previousReading()
    {
        return $this->belongsTo(MeterReading::class, 'previous_reading_id');
    }

    public function currentReading()
    {
        return $this->belongsTo(MeterReading::class, 'current_reading_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function adjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'bill_id');
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'bill_id');
    }
}
