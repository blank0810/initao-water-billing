<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'position_id',
        'status_id'
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function zoneAssignments()
    {
        return $this->hasMany(ZoneAssignment::class);
    }

    public function meterReadings()
    {
        return $this->hasMany(MeterReading::class, 'reader_employee_id');
    }
}
