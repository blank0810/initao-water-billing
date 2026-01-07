<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaAssignment extends Model
{
    protected $table = 'AreaAssignment';

    protected $primaryKey = 'area_assignment_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'area_id',
        'meter_reader_id',
        'effective_from',
        'effective_to',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    /**
     * Get the area that owns the area assignment
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'a_id');
    }

    /**
     * Get the meter reader that owns the area assignment
     */
    public function meterReader()
    {
        return $this->belongsTo(MeterReader::class, 'meter_reader_id', 'mr_id');
    }
}
