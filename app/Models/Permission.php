<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $primaryKey = 'permission_id';

    public $timestamps = false;

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'permission_name',
        'description',
    ];

    // Users Module
    public const USERS_VIEW = 'users.view';

    public const USERS_MANAGE = 'users.manage';

    // Roles Module
    public const ROLES_VIEW = 'roles.view';

    public const ROLES_MANAGE = 'roles.manage';

    // Customers Module
    public const CUSTOMERS_VIEW = 'customers.view';

    public const CUSTOMERS_MANAGE = 'customers.manage';

    // Billing Module
    public const BILLING_VIEW = 'billing.view';

    public const BILLING_GENERATE = 'billing.generate';

    public const BILLING_ADJUST = 'billing.adjust';

    // Payments Module
    public const PAYMENTS_VIEW = 'payments.view';

    public const PAYMENTS_PROCESS = 'payments.process';

    public const PAYMENTS_VOID = 'payments.void';

    // Meters Module
    public const METERS_VIEW = 'meters.view';

    public const METERS_READ = 'meters.read';

    public const METERS_MANAGE = 'meters.manage';

    // Reports Module
    public const REPORTS_VIEW = 'reports.view';

    public const REPORTS_EXPORT = 'reports.export';

    // Settings Module
    public const SETTINGS_MANAGE = 'settings.manage';

    /**
     * Get the roles for the permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions', 'permission_id', 'role_id');
    }

    /**
     * Get permission by name
     */
    public static function findByName(string $name): ?self
    {
        return static::where('permission_name', $name)->first();
    }

    /**
     * Get all permission names grouped by module
     */
    public static function getAllPermissionsByModule(): array
    {
        return [
            'users' => [self::USERS_VIEW, self::USERS_MANAGE],
            'roles' => [self::ROLES_VIEW, self::ROLES_MANAGE],
            'customers' => [self::CUSTOMERS_VIEW, self::CUSTOMERS_MANAGE],
            'billing' => [self::BILLING_VIEW, self::BILLING_GENERATE, self::BILLING_ADJUST],
            'payments' => [self::PAYMENTS_VIEW, self::PAYMENTS_PROCESS, self::PAYMENTS_VOID],
            'meters' => [self::METERS_VIEW, self::METERS_READ, self::METERS_MANAGE],
            'reports' => [self::REPORTS_VIEW, self::REPORTS_EXPORT],
            'settings' => [self::SETTINGS_MANAGE],
        ];
    }

    /**
     * Get all permission names as flat array
     */
    public static function getAllPermissionNames(): array
    {
        return [
            self::USERS_VIEW,
            self::USERS_MANAGE,
            self::ROLES_VIEW,
            self::ROLES_MANAGE,
            self::CUSTOMERS_VIEW,
            self::CUSTOMERS_MANAGE,
            self::BILLING_VIEW,
            self::BILLING_GENERATE,
            self::BILLING_ADJUST,
            self::PAYMENTS_VIEW,
            self::PAYMENTS_PROCESS,
            self::PAYMENTS_VOID,
            self::METERS_VIEW,
            self::METERS_READ,
            self::METERS_MANAGE,
            self::REPORTS_VIEW,
            self::REPORTS_EXPORT,
            self::SETTINGS_MANAGE,
        ];
    }

    /**
     * Get permissions grouped by module (for matrix view)
     */
    public static function getPermissionsByModule(): array
    {
        $permissions = static::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->permission_name);
            $module = ucfirst($parts[0] ?? 'other');

            if (! isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = $permission->permission_name;
        }

        return $grouped;
    }
}
