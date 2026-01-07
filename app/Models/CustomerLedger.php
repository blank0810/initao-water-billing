<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    protected $table = 'CustomerLedger';

    protected $primaryKey = 'ledger_entry_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'connection_id',
        'period_id',
        'txn_date',
        'post_ts',
        'source_type',
        'source_id',
        'source_line_no',
        'description',
        'debit',
        'credit',
        'user_id',
        'stat_id',
    ];

    protected $casts = [
        'txn_date' => 'date',
        'post_ts' => 'datetime',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the customer ledger entry
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'cust_id');
    }

    /**
     * Get the service connection that owns the customer ledger entry
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the period that owns the customer ledger entry
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the user that created the customer ledger entry
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the status associated with the customer ledger entry
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the source bill (polymorphic relationship)
     */
    public function sourceBill()
    {
        if ($this->source_type === 'BILL') {
            return $this->belongsTo(WaterBillHistory::class, 'source_id', 'bill_id');
        }

        return null;
    }

    /**
     * Get the source charge (polymorphic relationship)
     */
    public function sourceCharge()
    {
        if ($this->source_type === 'CHARGE') {
            return $this->belongsTo(CustomerCharge::class, 'source_id', 'charge_id');
        }

        return null;
    }

    /**
     * Get the source payment (polymorphic relationship)
     */
    public function sourcePayment()
    {
        if ($this->source_type === 'PAYMENT') {
            return $this->belongsTo(Payment::class, 'source_id', 'payment_id');
        }

        return null;
    }
}
