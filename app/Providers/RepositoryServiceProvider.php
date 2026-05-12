<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(
            'App\Repository\Interfaces\ProductRepositoryInterface',
            'App\Repository\ProductRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\AuthRepositoryInterface',
            'App\Repository\AuthRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\SupplierRepositoryInterface',
            'App\Repository\SupplierRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\UserRepositoryInterface',
            'App\Repository\UserRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\PurchaseOrderRepositoryInterface',
            'App\Repository\PurchaseOrderRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\GrnRepositoryInterface',
            'App\Repository\GrnRepository'
        );

        $this->app->bind(
            'App\Repository\Interfaces\IssueNoteRepositoryInterface',
            'App\Repository\IssueNoteRepository'
        );
    }

    public function boot()
    {
        //
    }
}
