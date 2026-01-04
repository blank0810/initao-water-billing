<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

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
    public function roles(): BelongsToMany
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

    // =========================================================================
    // RBAC Methods
    // =========================================================================

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->roles()->where('role_name', $roles)->exists();
        }

        return $this->roles()->whereIn('role_name', $roles)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    /**
     * Check if user has all of the given roles
     */
    public function hasAllRoles(array $roles): bool
    {
        $userRoleNames = $this->roles()->pluck('role_name')->toArray();
        return empty(array_diff($roles, $userRoleNames));
    }

    /**
     * Check if user has a specific permission (through roles)
     */
    public function hasPermission(string $permission): bool
    {
        // Super admin has all permissions
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permission) {
                $query->where('permission_name', $permission);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissions) {
                $query->whereIn('permission_name', $permissions);
            })
            ->exists();
    }

    /**
     * Get all permissions for the user (through roles)
     */
    public function getAllPermissions(): Collection
    {
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return Permission::all();
        }

        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('role_id', $this->roles()->pluck('role_id'));
        })->get();
    }

    /**
     * Assign a role to the user
     */
    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::findByName($role);
        }

        if ($role && !$this->hasRole($role->role_name)) {
            $this->roles()->attach($role->role_id);
        }
    }

    /**
     * Remove a role from the user
     */
    public function removeRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::findByName($role);
        }

        if ($role) {
            $this->roles()->detach($role->role_id);
        }
    }

    /**
     * Sync user roles
     */
    public function syncRoles(array $roleNames): void
    {
        $roleIds = Role::whereIn('role_name', $roleNames)->pluck('role_id')->toArray();
        $this->roles()->sync($roleIds);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::SUPER_ADMIN);
    }

    /**
     * Check if user is admin (either super_admin or admin)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([Role::SUPER_ADMIN, Role::ADMIN]);
    }
}
