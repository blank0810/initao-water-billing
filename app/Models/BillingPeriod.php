<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPeriod extends Model
{
    protected $table = 'billing_periods';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'year',
        'month',
        'code',
        'start_date',
        'end_date',
        'reading_deadline',
        'billing_date',
        'due_date',
        'status_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reading_deadline' => 'date',
        'billing_date' => 'date',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class);
    }

    public function waterBills()
    {
        return $this->hasMany(WaterBill::class);
    }

    public function waterRates()
    {
        return $this->hasMany(WaterRate::class);
    }
}
