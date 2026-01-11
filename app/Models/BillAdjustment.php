<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model
{
    protected $table = 'BillAdjustment';
    protected $primaryKey = 'bill_adjustment_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'bill_id',
        'bill_adjustment_type_id',
        'amount',
        'remarks',
        'created_at',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Get the water bill history that owns the bill adjustment
     */
    public function waterBillHistory()
    {
        return $this->belongsTo(WaterBillHistory::class, 'bill_id', 'bill_id');
    }

    /**
     * Get the bill adjustment type that owns the bill adjustment
     */
    public function billAdjustmentType()
    {
        return $this->belongsTo(BillAdjustmentType::class, 'bill_adjustment_type_id', 'bill_adjustment_type_id');
    }

    /**
     * Get the user that created the bill adjustment
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}