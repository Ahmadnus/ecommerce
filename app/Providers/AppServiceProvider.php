<?php

namespace App\Providers;

use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Interfaces\OrderRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
       
        // تمرير مصفوفة المفضلة لكل الصفحات التي تحتوي على منتجات
        View::composer('*', function ($view) {
            $wishlistedIds = [];
            
            if (Auth::check()) {
                // جلب الـ IDs للمنتجات المفضلة للمستخدم المسجل دخوله
                $wishlistedIds = Auth::user()->wishlistedProducts()
                    ->pluck('product_id')
                    ->toArray();
            }

            $view->with('wishlistedIds', $wishlistedIds);
        });
    }
}
