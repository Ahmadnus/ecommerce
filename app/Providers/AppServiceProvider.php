<?php

namespace App\Providers;

use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

/**
 * AppServiceProvider
 *
 * Binds repository interfaces to their concrete Eloquent implementations.
 * To swap implementations (e.g. for caching or a different DB), only change here.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings — the interface is what controllers/services depend on
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
