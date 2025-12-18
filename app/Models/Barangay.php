<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $table = 'barangays';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function puroks()
    {
        return $this->hasMany(Purok::class);
    }

    public function addresses()
    {
        return $this->hasMany(ConsumerAddress::class);
    }
}
