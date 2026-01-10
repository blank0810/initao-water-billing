<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_types';

    protected $primaryKey = 'ut_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'ut_desc',
        'status_id',
    ];

    /**
     * Get the status associated with the user type
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'stat_id');
    }

    /**
     * Get the users for the user type
     */
    public function users()
    {
        return $this->hasMany(User::class, 'u_type', 'ut_id');
    }
}
