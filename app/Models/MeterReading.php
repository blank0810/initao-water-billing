<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReading extends Model
{
    protected $table = 'meter_readings';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'meter_assignment_id',
        'billing_period_id',
        'reading_date',
        'reading_value',
        'previous_reading',
        'is_estimated',
        'estimated_reason',
        'reader_employee_id',
        'photo_url',
        'remarks'
    ];

    protected $casts = [
        'reading_date' => 'date',
        'reading_value' => 'decimal:3',
        'previous_reading' => 'decimal:3',
        'consumption' => 'decimal:3', // Generated column
        'is_estimated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(MeterAssignment::class, 'meter_assignment_id');
    }

    public function billingPeriod()
    {
        return $this->belongsTo(BillingPeriod::class);
    }

    public function reader()
    {
        return $this->belongsTo(Employee::class, 'reader_employee_id');
    }

    public function bill()
    {
        return $this->hasOne(WaterBill::class, 'current_reading_id');
    }
}
