<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerLedger extends Model
{
    protected $table = 'consumer_ledger';

    protected $primaryKey = 'cl_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'c_id',
        'cl_no',
        'debit',
        'credit',
        'balance',
        'create_date',
        'stat_id',
        'user_id',
        'or_no',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
        'create_date' => 'datetime',
    ];

    /**
     * Get the consumer that owns the consumer ledger
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class, 'c_id', 'c_id');
    }

    /**
     * Get the status associated with the consumer ledger
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the user that created the consumer ledger
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the water bills for the consumer ledger
     */
    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'cl_id', 'cl_id');
    }

    /**
     * Get the misc bills for the consumer ledger
     */
    public function miscBills()
    {
        return $this->hasMany(MiscBill::class, 'cl_id', 'cl_id');
    }
}
