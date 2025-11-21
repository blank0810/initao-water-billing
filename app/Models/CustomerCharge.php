<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCharge extends Model
{
    protected $table = 'CustomerCharge';
    protected $primaryKey = 'charge_id';
    public $timestamps = false;
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
        'created_at',
        'due_date',
        'stat_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'due_date' => 'date',
    ];

    /**
     * Get the customer that owns the customer charge
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the service application that owns the customer charge
     */
    public function serviceApplication()
    {
        return $this->belongsTo(ServiceApplication::class, 'application_id', 'application_id');
    }

    /**
     * Get the service connection that owns the customer charge
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the charge item that owns the customer charge
     */
    public function chargeItem()
    {
        return $this->belongsTo(ChargeItem::class, 'charge_item_id', 'charge_item_id');
    }

    /**
     * Get the status associated with the customer charge
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Accessor for total amount (computed column)
     */
    public function getTotalAmountAttribute()
    {
        return $this->quantity * $this->unit_amount;
    }
}