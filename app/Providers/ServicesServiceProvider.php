<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->app->bind(
            'App\Services\Interfaces\AuthServiceInterface',
            'App\Services\AuthService'
        );

        $this->app->bind(
            'App\Services\Interfaces\ProductServiceInterface',
            'App\Services\ProductService'
        );

        $this->app->bind(
            'App\Services\Interfaces\SupplierServiceInterface',
            'App\Services\SupplierService'
        );

        $this->app->bind(
            'App\Services\Interfaces\UserServiceInterface',
            'App\Services\UserService'
        );

        $this->app->bind(
            'App\Services\Interfaces\PurchaseOrderServiceInterface',
            'App\Services\PurchaseOrderService'
        );

        $this->app->bind(
            'App\Services\Interfaces\GrnServiceInterface',
            'App\Services\GrnService'
        );

        $this->app->bind(
            'App\Services\Interfaces\IssueNoteServiceInterface',
            'App\Services\IssueNoteService'
        );
    }

    public function boot()
    {
        //
    }
}
