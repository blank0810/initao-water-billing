<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyConfiguration extends Model
{
    protected $table = 'PenaltyConfiguration';

    protected $primaryKey = 'penalty_config_id';

    protected $fillable = [
        'rate_percentage',
        'is_active',
        'effective_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rate_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'effective_date' => 'date',
    ];

    private const DEFAULT_RATE = 0.10;

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * Get the active penalty rate as a decimal (e.g. 0.10 for 10%).
     */
    public static function getActiveRate(): float
    {
        $config = static::where('is_active', true)
            ->orderByDesc('effective_date')
            ->first();

        return $config ? (float) $config->rate_percentage / 100 : self::DEFAULT_RATE;
    }

    /**
     * Get the active penalty rate as a percentage (e.g. 10.00 for 10%).
     */
    public static function getActiveRatePercentage(): float
    {
        return static::getActiveRate() * 100;
    }
}
