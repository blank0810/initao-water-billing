<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purok extends Model
{
    use HasFactory;

    protected $table = 'purok';

    protected $primaryKey = 'p_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'p_desc',
        'stat_id',
    ];

    /**
     * Get the status associated with the purok
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the consumer addresses for the purok
     */
    public function consumerAddresses()
    {
        return $this->hasMany(ConsumerAddress::class, 'p_id', 'p_id');
    }
}
