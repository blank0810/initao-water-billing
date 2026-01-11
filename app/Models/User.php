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
        'name',
        'email',
        'password',
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
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    /**
     * Get the customer ledger entries created by the user
     */
    public function customerLedgerEntries()
    {
        return $this->hasMany(CustomerLedger::class, 'user_id', 'id');
    }

    /**
     * Get the bill adjustments created by the user
     */
    public function billAdjustments()
    {
        return $this->hasMany(BillAdjustment::class, 'user_id', 'id');
    }

    // =========================================================================
    // RBAC Methods
    // =========================================================================

    /**
     * Get loaded roles with permissions, loading once if needed.
     * Prevents N+1 queries by ensuring roles.permissions is loaded only once.
     */
    protected function getLoadedRoles(): Collection
    {
        if (! $this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        return $this->roles;
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string|array $roles): bool
    {
        $roleNames = $this->getLoadedRoles()->pluck('role_name');

        if (is_string($roles)) {
            return $roleNames->contains($roles);
        }

        return $roleNames->intersect($roles)->isNotEmpty();
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
        $userRoleNames = $this->getLoadedRoles()->pluck('role_name')->toArray();

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

        return $this->getLoadedRoles()
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('permission_name')
            ->contains($permission);
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return true;
        }

        return $this->getLoadedRoles()
            ->flatMap(fn ($role) => $role->permissions)
            ->pluck('permission_name')
            ->intersect($permissions)
            ->isNotEmpty();
    }

    /**
     * Get all permissions for the user (through roles)
     */
    public function getAllPermissions(): Collection
    {
        if ($this->hasRole(Role::SUPER_ADMIN)) {
            return Permission::all();
        }

        return $this->getLoadedRoles()
            ->flatMap(fn ($role) => $role->permissions)
            ->unique('permission_id');
    }

    /**
     * Assign a role to the user (atomic and idempotent)
     */
    public function assignRole(string|Role $role): void
    {
        if (is_string($role)) {
            $role = Role::findByName($role);
        }

        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->role_id]);
            // Refresh loaded relationship
            $this->load('roles.permissions');
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
            // Refresh loaded relationship
            $this->load('roles.permissions');
        }
    }

    /**
     * Sync user roles
     */
    public function syncRoles(array $roleNames): void
    {
        $roleIds = Role::whereIn('role_name', $roleNames)->pluck('role_id')->toArray();
        $this->roles()->sync($roleIds);
        // Refresh loaded relationship
        $this->load('roles.permissions');
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

    /**
     * Get the area assignments for the user (when user is a meter reader)
     */
    public function areaAssignments()
    {
        return $this->hasMany(AreaAssignment::class, 'user_id', 'id');
    }

    /**
     * Get active area assignments for the user
     */
    public function activeAreaAssignments()
    {
        return $this->areaAssignments()->active();
    }

    /**
     * Check if user is a meter reader
     */
    public function isMeterReader(): bool
    {
        return $this->hasRole(Role::METER_READER);
    }
}
