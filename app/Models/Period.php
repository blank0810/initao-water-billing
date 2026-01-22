<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $table = 'period';

    protected $primaryKey = 'per_id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'per_name',
        'per_code',
        'start_date',
        'end_date',
        'is_closed',
        'closed_at',
        'closed_by',
        'stat_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the status associated with the period
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the user who closed this period
     */
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    /**
     * Get the water bills for the period
     */
    public function waterBills()
    {
        return $this->hasMany(WaterBill::class, 'per_id', 'per_id');
    }

    /**
     * Get the water bill history for the period
     */
    public function waterBillHistory()
    {
        return $this->hasMany(WaterBillHistory::class, 'period_id', 'per_id');
    }

    /**
     * Get the misc bills for the period
     */
    public function miscBills()
    {
        return $this->hasMany(MiscBill::class, 'per_id', 'per_id');
    }

    /**
     * Get the meter readings for the period
     */
    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'period_id', 'per_id');
    }

    /**
     * Get the customer ledger entries for the period
     */
    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'period_id', 'per_id');
    }

    /**
     * Get the payment allocations for the period
     */
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'period_id', 'per_id');
    }

    /**
     * Get the reading schedules for the period
     */
    public function readingSchedules()
    {
        return $this->hasMany(ReadingSchedule::class, 'per_id', 'per_id');
    }

    /**
     * Get the water rates for this period
     */
    public function waterRates()
    {
        return $this->hasMany(WaterRate::class, 'period_id', 'per_id');
    }

    /**
     * Check if this period has custom rates
     */
    public function hasCustomRates(): bool
    {
        return $this->waterRates()->exists();
    }
}
