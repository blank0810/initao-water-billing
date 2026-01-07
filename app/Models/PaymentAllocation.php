<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    protected $table = 'PaymentAllocation';

    protected $primaryKey = 'payment_allocation_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'payment_id',
        'target_type',
        'target_id',
        'amount_applied',
        'period_id',
        'connection_id',
    ];

    protected $casts = [
        'amount_applied' => 'decimal:2',
    ];

    /**
     * Get the payment that owns the payment allocation
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the period that owns the payment allocation
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the service connection that owns the payment allocation
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the target bill (polymorphic relationship)
     */
    public function targetBill()
    {
        if ($this->target_type === 'BILL') {
            return $this->belongsTo(WaterBillHistory::class, 'target_id', 'bill_id');
        }

        return null;
    }

    /**
     * Get the target charge (polymorphic relationship)
     */
    public function targetCharge()
    {
        if ($this->target_type === 'CHARGE') {
            return $this->belongsTo(CustomerCharge::class, 'target_id', 'charge_id');
        }

        return null;
    }
}
