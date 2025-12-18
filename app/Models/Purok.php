<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purok extends Model
{
    protected $table = 'puroks';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'barangay_id',
        'status_id'
    ];

    public function barangay()
    {
        return $this->belongsTo(Barangay::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function addresses()
    {
        return $this->hasMany(ConsumerAddress::class);
    }
}
