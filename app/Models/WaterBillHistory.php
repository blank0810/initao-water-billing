<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterBillHistory extends Model
{
    use HasFactory;

    protected $table = 'water_bill_history';

    protected $primaryKey = 'bill_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'connection_id',
        'period_id',
        'prev_reading_id',
        'curr_reading_id',
        'consumption',
        'water_amount',
        'due_date',
        'adjustment_total',
        'is_meter_change',
        'old_assignment_id',
        'old_meter_consumption',
        'new_meter_consumption',
        'stat_id',
        'photo_path',
    ];

    protected $casts = [
        'consumption' => 'decimal:3',
        'water_amount' => 'decimal:2',
        'due_date' => 'date',
        'adjustment_total' => 'decimal:2',
        'is_meter_change' => 'boolean',
        'old_meter_consumption' => 'decimal:3',
        'new_meter_consumption' => 'decimal:3',
    ];

    /**
     * Get the service connection that owns the water bill history
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the period that owns the water bill history
     */
    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'per_id');
    }

    /**
     * Get the previous meter reading
     */
    public function previousReading()
    {
        return $this->belongsTo(MeterReading::class, 'prev_reading_id', 'reading_id');
    }

    /**
     * Get the current meter reading
     */
    public function currentReading()
    {
        return $this->belongsTo(MeterReading::class, 'curr_reading_id', 'reading_id');
    }

    /**
     * Get the status associated with the water bill history
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the bill adjustments for the water bill history
     */
    public function billAdjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'bill_id', 'bill_id');
    }

    /**
     * Accessor for total amount (computed column)
     */
    public function getTotalAmountAttribute()
    {
        return $this->water_amount + $this->adjustment_total;
    }

    /**
     * Get the full URL for the reading photo, or a default placeholder.
     */
    public function getPhotoUrlAttribute(): string
    {
        return asset($this->photo_path ?? 'images/no-reading-photo.svg');
    }

    /**
     * Check if this bill has an actual reading photo.
     */
    public function getHasPhotoAttribute(): bool
    {
        return ! empty($this->photo_path);
    }

    /**
     * Get the old meter assignment (for meter change bills)
     */
    public function oldMeterAssignment()
    {
        return $this->belongsTo(MeterAssignment::class, 'old_assignment_id', 'assignment_id');
    }
}
