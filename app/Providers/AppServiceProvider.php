<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();

        // Define the view composer
        view()->composer('*', function ($view) {
            if (request()->is('tyre*')) {
                session(['active_system' => 'tyre']);
            } elseif (
                request()->is('admin/dashboard') || 
                request()->is('admin/supplier*') || 
                request()->is('admin/products*') || 
                request()->is('admin/stock*') || 
                request()->is('admin/reports*') || 
                request()->is('purchase-orders*') || 
                request()->is('get-all-grns*') || 
                request()->is('get-all-issue-note*')
            ) {
                session(['active_system' => 'iffs']);
            }

            // Get the user's permissions
            if (Auth::check()) {
                $LoginUserRole = Auth::user()->role_id;
                
                $getLoginUserPermission = DB::table('role_permissions')
                    ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
                    ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                    ->select('role_permissions.*', 'roles.role_name', 'permissions.name', 'permissions.slug')
                    ->where('role_permissions.role_id', $LoginUserRole)
                    ->get();
                // Pass the data to the view
                $view->with('getLoginUserPermission', $getLoginUserPermission);
            }
        });
    }
}
