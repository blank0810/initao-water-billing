<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillAdjustmentType extends Model
{
    use HasFactory;

    protected $table = 'BillAdjustmentType';

    protected $primaryKey = 'bill_adjustment_type_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'direction',
        'stat_id',
    ];

    /**
     * Get the status associated with the bill adjustment type
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
    }

    /**
     * Get the bill adjustments for the bill adjustment type
     */
    public function billAdjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'bill_adjustment_type_id', 'bill_adjustment_type_id');
    }
}
