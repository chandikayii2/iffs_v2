<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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
        // Define the view composer
        view()->composer('*', function ($view) {
            // Get the user's permissions
            if (Auth::check()) {
                $LoginUserRole = Auth::user()->role_id;
                // rest of the code
                $getLoginUserPermission = DB::table('role_permissions')
                    ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                    ->select('role_permissions.*', 'roles.role_name', 'permissions.name', 'permissions.slug')
                    ->where('role_permissions.role_id', $LoginUserRole)
                    ->get();
                // Pass the data to the view
                $view->with('getLoginUserPermission', $getLoginUserPermission);
            } else {
                // handle unauthenticated user
            }
        });
    }
}
