<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (App::environment(['local'])) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $permissions = [
            'edit-emailformats', 'view-emailformats', 'edit-emailtemplates', 'show-emailtemplates', 'view-emailtemplates', 'view-role', 'show-role', 'add-role', 'edit-role', 'delete-role', 'bulkDelete-role', 'import-role', 'export-role', 'role-imports', 'view-user', 'show-user', 'add-user', 'edit-user', 'delete-user', 'bulkDelete-user', 'import-user', 'export-user', 'user-imports', 'view-brand', 'show-brand', 'add-brand', 'edit-brand', 'delete-brand', 'bulkDelete-brand', 'import-brand', 'export-brand', 'brand-imports', 'view-product', 'show-product', 'add-product', 'edit-product', 'delete-product', 'bulkDelete-product', 'import-product', 'export-product', 'product-imports',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission, $user->role_id);
            });
        }
    }
}
