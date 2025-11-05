<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'email',
        'name',
        'u_type',
        'status_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the user type that owns the user
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'u_type', 'ut_id');
    }

    /**
     * Get the status that owns the user
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'stat_id');
    }

    /**
     * Get the roles for the user
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    /**
     * Get the payments created by the user
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'user_id');
    }

    /**
     * Get the customer ledger entries created by the user
     */
    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'user_id', 'user_id');
    }

    /**
     * Get the bill adjustments created by the user
     */
    public function billAdjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'user_id', 'user_id');
    }
}
