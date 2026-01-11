<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    protected $table = 'barangay';

    protected $primaryKey = 'b_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'b_desc',
        'b_code',
        'stat_id',
    ];

    /**
     * Get the puroks for the barangay
     */
    public function puroks()
    {
        return $this->hasMany(Purok::class, 'b_id', 'b_id');
    }

    /**
     * Get the status associated with the barangay
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the consumer addresses for the barangay
     */
    public function consumerAddresses()
    {
        return $this->hasMany(ConsumerAddress::class, 'b_id', 'b_id');
    }
}
