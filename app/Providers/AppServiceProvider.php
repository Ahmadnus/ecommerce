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
        $this->app->singleton(\App\Services\SmsService::class);
        
    }

  public function boot(): void
{   
    \Illuminate\Support\Facades\View::composer('*', function ($view) {
        // 1. استثناء لوحة التحكم والـ Livewire لتجنب المشاكل
        if (request()->is('manager*') || request()->is('livewire*') || request()->is('admin*')) {
            return;
        }

        // 2. جلب اللوغو والإعدادات (حل مشكلة Undefined variable $logoUrl)
        $siteSettings = \App\Models\Setting::pluck('value', 'key');
        $logoPath = $siteSettings['site_logo'] ?? null;
        $logoUrl = $logoPath ? asset('storage/' . $logoPath) : asset('images/default-logo.png');

        // 3. منطق المفضلة (Wishlist)
        $wishlistedIds = [];
        if (auth()->check()) {
            $wishlistedIds = auth()->user()->wishlistedProducts()
                ->pluck('product_id')
                ->toArray();
        }

        // 4. تمرير كل المتغيرات لجميع الصفحات
        $view->with([
            'logoUrl' => $logoUrl,
            'siteSettings' => $siteSettings,
            'wishlistedIds' => $wishlistedIds
        ]);
    });

}
}
