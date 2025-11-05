<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApplication extends Model
{
    protected $table = 'ServiceApplication';
    protected $primaryKey = 'application_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'address_id',
        'submitted_at',
        'stat_id'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Get the customer that owns the service application
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the address that owns the service application
     */
    public function address()
    {
        return $this->belongsTo(ConsumerAddress::class, 'address_id', 'ca_id');
    }

    /**
     * Get the status associated with the service application
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the customer charges for the service application
     */
    public function customerCharges()
    {
        return $this->hasMany(CustomerCharge::class, 'application_id', 'application_id');
    }
}