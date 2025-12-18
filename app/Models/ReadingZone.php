<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingZone extends Model
{
    protected $table = 'reading_zones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function assignments()
    {
        return $this->hasMany(ZoneAssignment::class, 'zone_id');
    }

    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class, 'zone_id');
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'zone_id');
    }
}
