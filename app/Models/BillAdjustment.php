<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model
{
    protected $table = 'bill_adjustments';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'bill_id',
        'adjustment_type_id',
        'old_reading',
        'new_reading',
        'amount',
        'remarks',
        'user_id'
    ];

    protected $casts = [
        'old_reading' => 'decimal:3',
        'new_reading' => 'decimal:3',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(WaterBill::class, 'bill_id');
    }

    public function adjustmentType()
    {
        return $this->belongsTo(BillAdjustmentType::class, 'adjustment_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
