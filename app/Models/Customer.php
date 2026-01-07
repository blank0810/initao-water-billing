<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';

    protected $primaryKey = 'cust_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'create_date',
        'cust_last_name',
        'cust_first_name',
        'cust_middle_name',
        'ca_id',
        'land_mark',
        'stat_id',
        'c_type',
        'resolution_no',
    ];

    protected $casts = [
        'create_date' => 'datetime',
    ];

    /**
     * Get the status associated with the customer
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the address associated with the customer
     */
    public function address()
    {
        return $this->belongsTo(ConsumerAddress::class, 'ca_id', 'ca_id');
    }

    /**
     * Get the consumers for the customer
     */
    public function consumers()
    {
        return $this->hasMany(Consumer::class, 'cust_id', 'cust_id');
    }

    /**
     * Get the service applications for the customer
     */
    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the service connections for the customer
     */
    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the customer charges for the customer
     */
    public function customerCharges()
    {
        return $this->hasMany(CustomerCharge::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the customer ledger entries for the customer
     */
    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the payments made by the customer
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payer_id', 'cust_id');
    }
}
