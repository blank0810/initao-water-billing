<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerAddress extends Model
{
    protected $table = 'consumer_address';

    protected $primaryKey = 'ca_id';

    public $timestamps = true;

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'p_id',
        'b_id',
        't_id',
        'prov_id',
        'stat_id',
    ];

    /**
     * Get the status associated with the consumer address
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the province that owns the consumer address
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'prov_id', 'prov_id');
    }

    /**
     * Get the town that owns the consumer address
     */
    public function town()
    {
        return $this->belongsTo(Town::class, 't_id', 't_id');
    }

    /**
     * Get the barangay that owns the consumer address
     */
    public function barangay()
    {
        return $this->belongsTo(Barangay::class, 'b_id', 'b_id');
    }

    /**
     * Get the purok that owns the consumer address
     */
    public function purok()
    {
        return $this->belongsTo(Purok::class, 'p_id', 'p_id');
    }

    /**
     * Get the customers for the consumer address
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'ca_id', 'ca_id');
    }

    /**
     * Get the service applications for the consumer address
     */
    public function serviceApplications()
    {
        return $this->hasMany(ServiceApplication::class, 'address_id', 'ca_id');
    }

    /**
     * Get the service connections for the consumer address
     */
    public function serviceConnections()
    {
        return $this->hasMany(ServiceConnection::class, 'address_id', 'ca_id');
    }
}
