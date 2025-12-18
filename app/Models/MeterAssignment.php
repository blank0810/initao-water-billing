<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterAssignment extends Model
{
    protected $table = 'meter_assignments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'connection_id',
        'meter_id',
        'installed_at',
        'removed_at',
        'install_reading',
        'removal_reading'
    ];

    protected $casts = [
        'installed_at' => 'date',
        'removed_at' => 'date',
        'install_reading' => 'decimal:3',
        'removal_reading' => 'decimal:3',
    ];

    public function connection()
    {
        return $this->belongsTo(ServiceConnection::class, 'connection_id');
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class);
    }

    public function readings()
    {
        return $this->hasMany(MeterReading::class, 'meter_assignment_id');
    }
}
