<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $table = 'town';

    protected $primaryKey = 't_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        't_desc',
        'stat_id',
    ];

    /**
     * Get the status associated with the town
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the consumer addresses for the town
     */
    public function consumerAddresses()
    {
        return $this->hasMany(ConsumerAddress::class, 't_id', 't_id');
    }
}
