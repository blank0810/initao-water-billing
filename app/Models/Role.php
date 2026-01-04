<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'role_id';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'role_name',
        'description'
    ];

    // Role name constants
    public const SUPER_ADMIN = 'super_admin';
    public const ADMIN = 'admin';
    public const BILLING_OFFICER = 'billing_officer';
    public const METER_READER = 'meter_reader';
    public const CASHIER = 'cashier';
    public const VIEWER = 'viewer';

    /**
     * Get the users for the role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id');
    }

    /**
     * Get the permissions for the role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()->where('permission_name', $permissionName)->exists();
    }

    /**
     * Get role by name
     */
    public static function findByName(string $name): ?self
    {
        return static::where('role_name', $name)->first();
    }

    /**
     * Get all available role names
     */
    public static function getAllRoleNames(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::BILLING_OFFICER,
            self::METER_READER,
            self::CASHIER,
            self::VIEWER,
        ];
    }
}