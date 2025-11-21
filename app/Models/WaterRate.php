<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaterRate extends Model
{
    protected $table = 'water_rates';
    protected $primaryKey = 'wr_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'rate_desc',
        'rate',
        'stat_id'
    ];

    protected $casts = [
        'rate' => 'decimal:5',
    ];

    /**
     * Get the status associated with the water rate
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}