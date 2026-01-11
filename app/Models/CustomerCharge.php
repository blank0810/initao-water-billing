<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerCharge extends Model
{
    protected $table = 'CustomerCharge';


    protected $primaryKey = 'charge_id';

    public $timestamps = true;

    public $incrementing = true;


    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'application_id',
        'connection_id',
        'charge_item_id',
        'description',
        'quantity',
        'unit_amount',
        'due_date',
        'stat_id',
        'stat_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_amount' => 'decimal:2',
        'due_date' => 'date',
    ];

    /**
     * Attributes to append to JSON serialization
     */
    protected $appends = [
        'total_amount',
        'paid_amount',
        'remaining_amount',
    ];

    /**
     * Get the customer that owns the charge
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the service application that owns the charge
     */
    public function serviceApplication(): BelongsTo
    {
        return $this->belongsTo(ServiceApplication::class, 'application_id', 'application_id');
    }

    /**
     * Get the service connection that owns the charge
     */
    public function serviceConnection(): BelongsTo
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the charge item definition
     */
    public function chargeItem(): BelongsTo
    {
        return $this->belongsTo(ChargeItem::class, 'charge_item_id', 'charge_item_id');
    }

    /**
     * Get the status of the charge
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get payment allocations for this charge (polymorphic)
     */
    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'target_id', 'charge_id')
            ->where('target_type', 'CHARGE');
    }

    /**
     * Accessor for total amount (quantity * unit_amount)
     */
    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->quantity * $this->unit_amount);
    }

    /**
     * Accessor for paid amount (sum of all allocations)
     */
    public function getPaidAmountAttribute(): float
    {
        return (float) $this->paymentAllocations()->sum('amount_applied');
    }

    /**
     * Accessor for remaining unpaid amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return (float) max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * Check if the charge is fully paid
     */
    public function isPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Check if the charge is partially paid
     */
    public function isPartiallyPaid(): bool
    {
        return $this->paid_amount > 0 && ! $this->isPaid();
    }
}
