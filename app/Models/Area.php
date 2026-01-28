<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $table = 'area';

    protected $primaryKey = 'a_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'a_desc',
        'stat_id',
    ];

    /**
     * Get the status associated with the area
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the area assignments for the area
     */
    public function areaAssignments()
    {
        return $this->hasMany(AreaAssignment::class, 'area_id', 'a_id');
    }

    /**
     * Get active area assignments (where effective_to is null or not yet reached)
     */
    public function activeAreaAssignments()
    {
        return $this->areaAssignments()->where(function ($q) {
            $q->whereNull('effective_to')
                ->orWhere('effective_to', '>=', now()->format('Y-m-d'));
        });
    }

    /**
     * Get the consumers for the area
     */
    public function consumers()
    {
        return $this->hasMany(Consumer::class, 'a_id', 'a_id');
    }

    /**
     * Get the reading schedules for the area
     */
    public function readingSchedules()
    {
        return $this->hasMany(ReadingSchedule::class, 'a_id', 'a_id');
    }

    /**
     * Get the service connections for the area
     */
    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'area_id', 'a_id');
    }
}
