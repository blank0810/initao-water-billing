<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscBill extends Model
{
    protected $table = 'misc_bill';

    protected $primaryKey = 'mb_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'mref_id',
        'cl_id',
        'per_id',
        'create_date',
        'amount',
        'stat_id',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the misc reference that owns the misc bill
     */
    public function miscReference()
    {
        return $this->belongsTo(MiscReference::class, 'mref_id', 'mref_id');
    }

    /**
     * Get the consumer ledger that owns the misc bill
     */
    public function consumerLedger()
    {
        return $this->belongsTo(ConsumerLedger::class, 'cl_id', 'cl_id');
    }

    /**
     * Get the period that owns the misc bill
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'per_id', 'per_id');
    }

    /**
     * Get the status associated with the misc bill
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}
