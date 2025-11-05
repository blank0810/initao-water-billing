<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $primaryKey = 'stat_id';
    protected $fillable = ['stat_desc'];
    public $timestamps = false;

    // Status constants
    public const PENDING = 'PENDING';
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';

    /**
     * Get the ID of a status by its description
     */
    public static function getIdByDescription(string $description): ?int
    {
        return static::where('stat_desc', $description)->value('stat_id');
    }
}
