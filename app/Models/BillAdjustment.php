<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAdjustment extends Model
{
    use HasFactory;

    protected $table = 'BillAdjustment';

    protected $primaryKey = 'bill_adjustment_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'bill_id',
        'bill_adjustment_type_id',
        'amount',
        'old_consumption',
        'new_consumption',
        'old_amount',
        'new_amount',
        'adjustment_category',
        'remarks',
        'created_at',
        'user_id',
        'stat_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'old_consumption' => 'decimal:3',
        'new_consumption' => 'decimal:3',
        'old_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Adjustment category constants.
     */
    public const CATEGORY_CONSUMPTION = 'consumption';
    public const CATEGORY_AMOUNT = 'amount';
    public const CATEGORY_BOTH = 'both';
    public const CATEGORY_OTHER = 'other';

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

    /**
     * Get the status associated with the bill adjustment
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Calculate the consumption difference.
     */
    public function getConsumptionDifferenceAttribute(): ?float
    {
        if ($this->old_consumption === null || $this->new_consumption === null) {
            return null;
        }

        return $this->new_consumption - $this->old_consumption;
    }

    /**
     * Calculate the amount difference.
     */
    public function getAmountDifferenceAttribute(): ?float
    {
        if ($this->old_amount === null || $this->new_amount === null) {
            return null;
        }

        return $this->new_amount - $this->old_amount;
    }

    /**
     * Check if this is a consumption adjustment.
     */
    public function isConsumptionAdjustment(): bool
    {
        return in_array($this->adjustment_category, [self::CATEGORY_CONSUMPTION, self::CATEGORY_BOTH]);
    }

    /**
     * Check if this is an amount adjustment.
     */
    public function isAmountAdjustment(): bool
    {
        return in_array($this->adjustment_category, [self::CATEGORY_AMOUNT, self::CATEGORY_BOTH]);
    }

    /**
     * Scope for consumption adjustments.
     */
    public function scopeConsumptionAdjustments($query)
    {
        return $query->whereIn('adjustment_category', [self::CATEGORY_CONSUMPTION, self::CATEGORY_BOTH]);
    }

    /**
     * Scope for amount adjustments.
     */
    public function scopeAmountAdjustments($query)
    {
        return $query->whereIn('adjustment_category', [self::CATEGORY_AMOUNT, self::CATEGORY_BOTH]);
    }

    /**
     * Scope for active adjustments.
     */
    public function scopeActive($query)
    {
        return $query->where('stat_id', Status::getIdByDescription(Status::ACTIVE) ?? 2);
    }
}
