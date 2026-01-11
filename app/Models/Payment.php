<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'Payment';
    protected $primaryKey = 'payment_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'receipt_no',
        'payer_id',
        'payment_date',
        'amount_received',
        'created_at',
        'user_id',
        'stat_id'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_received' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the customer (payer) that owns the payment
     */
    public function payer()
    {
        return $this->belongsTo(Customer::class, 'payer_id', 'cust_id');
    }

    /**
     * Get the user that created the payment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the status associated with the payment
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the payment allocations for the payment
     */
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'payment_id', 'payment_id');
    }
}