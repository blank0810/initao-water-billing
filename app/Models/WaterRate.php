<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterRate extends Model
{
    protected $table = 'water_rates';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'account_type_id',
        'billing_period_id',
        'min_range',
        'max_range',
        'rate_value',
        'rate_increment',
        'effective_from',
        'effective_to',
        'status_id',
        'user_id'
    ];

    protected $casts = [
        'rate_value' => 'decimal:2',
        'rate_increment' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function billingPeriod()
    {
        return $this->belongsTo(BillingPeriod::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
