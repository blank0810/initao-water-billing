<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    protected $table = 'payment_allocations';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'payment_id',
        'bill_id',
        'charge_id',
        'amount',
        'type',
        'status_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function bill()
    {
        return $this->belongsTo(WaterBill::class);
    }

    public function charge()
    {
        return $this->belongsTo(CustomerCharge::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
