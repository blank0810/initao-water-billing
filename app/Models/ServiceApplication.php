<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'application_number',
        'submitted_at',
        'approved_at',
        'approved_by',
        'remarks',
        'stat_id',
        // Workflow columns
        'verified_at',
        'verified_by',
        'paid_at',
        'payment_id',
        'scheduled_at',
        'scheduled_connection_date',
        'scheduled_by',
        'connected_at',
        'connection_id',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'verified_at' => 'datetime',
        'paid_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'scheduled_connection_date' => 'date',
        'connected_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
    public function customerCharges(): HasMany
    {
        return $this->hasMany(CustomerCharge::class, 'application_id', 'application_id');
    }

    /**
     * Get the user who verified the application
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the payment associated with the application
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the user who scheduled the connection
     */
    public function scheduler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by');
    }

    /**
     * Get the service connection created from this application
     */
    public function serviceConnection(): BelongsTo
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the user who rejected the application
     */
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who approved the application (legacy)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
