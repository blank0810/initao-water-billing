<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceConnection extends Model
{
    protected $table = 'ServiceConnection';

    protected $primaryKey = 'connection_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'account_no',
        'customer_id',
        'address_id',
        'account_type_id',
        'started_at',
        'ended_at',
        'stat_id',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    /**
     * Get the customer that owns the service connection
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the address that owns the service connection
     */
    public function address()
    {
        return $this->belongsTo(ConsumerAddress::class, 'address_id', 'ca_id');
    }

    /**
     * Get the account type that owns the service connection
     */
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'account_type_id', 'at_id');
    }

    /**
     * Get the status associated with the service connection
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the meter assignments for the service connection
     */
    public function meterAssignments()
    {
        return $this->hasMany(MeterAssignment::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the water bill history for the service connection
     */
    public function waterBillHistory()
    {
        return $this->hasMany(WaterBillHistory::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the customer charges for the service connection
     */
    public function customerCharges()
    {
        return $this->hasMany(CustomerCharge::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the customer ledger entries for the service connection
     */
    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the payment allocations for the service connection
     */
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'connection_id', 'connection_id');
    }
}
