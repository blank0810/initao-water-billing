<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerGates();
        $this->registerBladeDirectives();
        $this->registerAuthEventListeners();
    }

    /**
     * Register authorization gates
     */
    protected function registerGates(): void
    {
        // Super admin gate - bypass all permission checks
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole(Role::SUPER_ADMIN)) {
                return true;
            }

            return null; // Let other gates decide
        });

        // Dynamic permission gates - allows using @can('permission.name') in Blade
        $permissions = Permission::getAllPermissionNames();

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        // Role-based gates for convenience
        Gate::define('is-super-admin', fn (User $user) => $user->hasRole(Role::SUPER_ADMIN));
        Gate::define('is-admin', fn (User $user) => $user->hasRole(Role::ADMIN));
        Gate::define('is-billing-officer', fn (User $user) => $user->hasRole(Role::BILLING_OFFICER));
        Gate::define('is-meter-reader', fn (User $user) => $user->hasRole(Role::METER_READER));
        Gate::define('is-cashier', fn (User $user) => $user->hasRole(Role::CASHIER));
        Gate::define('is-viewer', fn (User $user) => $user->hasRole(Role::VIEWER));
    }

    /**
     * Register custom Blade directives
     */
    protected function registerBladeDirectives(): void
    {
        // @role('admin') ... @endrole
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // @hasrole('admin') ... @endhasrole (alias)
        Blade::directive('hasrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endhasrole', function () {
            return '<?php endif; ?>';
        });

        // @anyrole(['admin', 'billing_officer']) ... @endangyrole
        Blade::directive('anyrole', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasAnyRole({$expression})): ?>";
        });

        Blade::directive('endangyrole', function () {
            return '<?php endif; ?>';
        });

        // @permission('billing.generate') ... @endpermission
        Blade::directive('permission', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasPermission({$expression})): ?>";
        });

        Blade::directive('endpermission', function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * Register authentication event listeners for activity logging
     */
    protected function registerAuthEventListeners(): void
    {
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
        Event::listen(Failed::class, LogFailedLogin::class);
    }
}
