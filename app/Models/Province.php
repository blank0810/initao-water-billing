<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'province';
    protected $primaryKey = 'prov_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'prov_desc',
        'stat_id'
    ];

    /**
     * Get the status associated with the province
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the towns for the province
     */
    public function towns()
    {
        return $this->hasMany(Town::class, 'prov_id', 'prov_id');
    }

    /**
     * Get the consumer addresses for the province
     */
    public function consumerAddresses()
    {
        return $this->hasMany(ConsumerAddress::class, 'prov_id', 'prov_id');
    }
}