<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterAssignment extends Model
{
    use HasFactory;

    protected $table = 'MeterAssignment';

    protected $primaryKey = 'assignment_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'connection_id',
        'meter_id',
        'installed_at',
        'removed_at',
        'install_read',
        'removal_read',
    ];

    protected $casts = [
        'installed_at' => 'date',
        'removed_at' => 'date',
        'install_read' => 'decimal:3',
        'removal_read' => 'decimal:3',
    ];

    /**
     * Get the service connection that owns the meter assignment
     */
    public function serviceConnection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id', 'connection_id');
    }

    /**
     * Get the meter that owns the meter assignment
     */
    public function meter()
    {
        return $this->belongsTo(Meter::class, 'meter_id', 'mtr_id');
    }

    /**
     * Get the meter readings for the meter assignment
     */
    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'assignment_id', 'assignment_id');
    }
}
