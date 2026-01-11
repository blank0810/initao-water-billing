<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerMeter extends Model
{
    protected $table = 'consumer_meters';

    protected $primaryKey = 'cm_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'mr_id',
        'create_date',
        'install_date',
        'initial_readout',
        'last_reading',
        'pulled_out_at',
        'stat_id',
        'user_id',
    ];

    protected $casts = [
        'create_date' => 'datetime',
        'install_date' => 'datetime',
        'initial_readout' => 'decimal:2',
        'last_reading' => 'decimal:2',
        'pulled_out_at' => 'datetime',
    ];

    /**
     * Get the meter reader that owns the consumer meter
     */
    public function meterReader()
    {
        return $this->belongsTo(MeterReader::class, 'mr_id', 'mr_id');
    }

    /**
     * Get the status associated with the consumer meter
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the user that created the consumer meter
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the consumers for the consumer meter
     */
    public function consumers()
    {
        return $this->hasMany(Consumer::class, 'cm_id', 'cm_id');
    }

    /**
     * Get the meter readings for the consumer meter
     */
    public function meterReadings()
    {
        return $this->hasMany(MeterReadingOld::class, 'cm_id', 'cm_id');
    }
}
