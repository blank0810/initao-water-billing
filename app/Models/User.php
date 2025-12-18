<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'email',
        'name',
        'user_type_id',
        'last_login_at',
        'status_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
                    ->withPivot('assigned_at');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class);
    }

    public function billAdjustments()
    {
        return $this->hasMany(BillAdjustment::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function waterRates()
    {
        return $this->hasMany(WaterRate::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
