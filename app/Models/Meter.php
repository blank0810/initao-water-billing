<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meter extends Model
{
    protected $table = 'meters';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'serial_number',
        'brand',
        'size',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function assignments()
    {
        return $this->hasMany(MeterAssignment::class);
    }
}
