<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MiscReference extends Model
{
    protected $table = 'misc_reference';

    protected $primaryKey = 'mref_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'ref_type',
        'stat_id',
    ];

    /**
     * Get the status associated with the misc reference
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }
}
