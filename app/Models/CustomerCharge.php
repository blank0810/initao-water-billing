<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCharge extends Model
{
    protected $table = 'customer_charges';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_id',
        'application_id',
        'connection_id',
        'charge_item_id',
        'quantity',
        'amount',
        'due_date',
        'status_id'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function application()
    {
        return $this->belongsTo(ServiceApplication::class);
    }

    public function connection()
    {
        return $this->belongsTo(ServiceConnection::class);
    }

    public function chargeItem()
    {
        return $this->belongsTo(ChargeItem::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'charge_id');
    }
}
