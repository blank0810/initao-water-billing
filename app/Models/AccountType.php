<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    protected $table = 'account_types';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'rate_category',
        'description',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class);
    }

    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class);
    }

    public function waterRates()
    {
        return $this->hasMany(WaterRate::class);
    }
}
