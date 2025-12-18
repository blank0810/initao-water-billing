<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'role_user';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'assigned_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];
}
