<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $primaryKey = 'stat_id';


    protected $fillable = ['stat_desc'];


    public $timestamps = false;

    // Status constants - Base
    public const PENDING = 'PENDING';


    public const ACTIVE = 'ACTIVE';


    public const INACTIVE = 'INACTIVE';

    // Status constants - Application Workflow
    public const VERIFIED = 'VERIFIED';

    public const PAID = 'PAID';

    public const SCHEDULED = 'SCHEDULED';

    public const CONNECTED = 'CONNECTED';

    public const REJECTED = 'REJECTED';

    public const CANCELLED = 'CANCELLED';

    // Status constants - Connection Lifecycle
    public const SUSPENDED = 'SUSPENDED';

    public const DISCONNECTED = 'DISCONNECTED';

    // Status constants - Reading Schedule
    public const COMPLETED = 'COMPLETED';

    /**
     * Get the ID of a status by its description
     */
    public static function getIdByDescription(string $description): ?int
    {
        return static::where('stat_desc', $description)->value('stat_id');
    }
}
