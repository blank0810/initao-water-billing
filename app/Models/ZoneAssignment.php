<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoneAssignment extends Model
{
    protected $table = 'zone_assignments';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'zone_id',
        'employee_id',
        'effective_from',
        'effective_to'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function zone()
    {
        return $this->belongsTo(ReadingZone::class, 'zone_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
