<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $table = 'payment_transactions';

    protected $primaryKey = 'pt_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'or_no',
        'create_date',
        'payment_date',
        'amount_tendered',
        'paid_amount',
        'amount_diff',
        'stat_id',
        'user_id',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'payment_date' => 'datetime',
        'amount_tendered' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'amount_diff' => 'decimal:2',
    ];

    /**
     * Get the status associated with the payment transaction
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the user that created the payment transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
