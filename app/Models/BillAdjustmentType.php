<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillAdjustmentType extends Model
{
    protected $table = 'bill_adjustment_types';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'direction',
        'description',
        'status_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function adjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'adjustment_type_id');
    }
}
