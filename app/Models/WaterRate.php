<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterRate extends Model
{
    use HasFactory;

    protected $table = 'water_rates';

    protected $primaryKey = 'wr_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'period_id',
        'class_id',
        'range_id',
        'range_min',
        'range_max',
        'rate_val',
        'stat_id',
    ];

    protected $casts = [
        'period_id' => 'integer',
        'class_id' => 'integer',
        'range_id' => 'integer',
        'range_min' => 'integer',
        'range_max' => 'integer',
        'rate_val' => 'decimal:2',
    ];

    /**
     * Get the status associated with the water rate.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the period this rate belongs to (nullable for default rates).
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the account type (class) for this rate.
     */
    public function accountType()
    {
        return $this->belongsTo(AccountType::class, 'class_id', 'at_id');
    }

    /**
     * Scope for default rates (no period assigned).
     */
    public function scopeDefault($query)
    {
        return $query->whereNull('period_id');
    }

    /**
     * Scope for rates belonging to a specific period.
     */
    public function scopeForPeriod($query, int $periodId)
    {
        return $query->where('period_id', $periodId);
    }

    /**
     * Scope for rates belonging to a specific class (account type).
     */
    public function scopeForClass($query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope for active rates.
     */
    public function scopeActive($query)
    {
        return $query->where('stat_id', Status::getIdByDescription(Status::ACTIVE));
    }

    /**
     * Calculate the charge for a given consumption using this rate tier.
     *
     * @param  int  $consumption  Total consumption in cu.m
     * @return float Calculated charge amount
     */
    public function calculateCharge(int $consumption): float
    {
        if ($consumption < $this->range_min) {
            return 0;
        }

        return $consumption * (float) $this->rate_val;
    }

    /**
     * Get a formatted description of this rate tier.
     */
    public function getDescription(): string
    {
        $range = $this->range_max >= 999
            ? "{$this->range_min}+ cu.m"
            : "{$this->range_min}-{$this->range_max} cu.m";

        return "Tier {$this->range_id}: {$range}";
    }
}
