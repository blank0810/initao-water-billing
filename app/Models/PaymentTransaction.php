<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'payment_id',
        'bill_id',
        'reference_number',
        'transaction_type',
        'amount',
        'applied_to_type',
        'applied_to_id',
        'notes',
        'processed_by',
        'stat_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the payment associated with the transaction
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    /**
     * Get the bill associated with the transaction
     */
    public function bill(): BelongsTo
    {
        return $this->belongsTo(WaterBill::class, 'bill_id', 'wb_id');
    }

    /**
     * Get the polymorphic applied_to relation
     */
    public function appliedTo(): MorphTo
    {
        return $this->morphTo('applied_to', 'applied_to_type', 'applied_to_id');
    }

    /**
     * Get the user that processed this transaction
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    /**
     * Get the status associated with the transaction
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}

