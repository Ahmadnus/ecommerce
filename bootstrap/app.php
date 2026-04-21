<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
 
    // 1. تسجيل الـ Aliases (للاستخدام داخل الـ Routes)
   $middleware->alias([
            // Spatie Permissions
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            
            // الـ Middlewares الجديدة التي طلبت إضافتها
            'admin.route.only'   => \App\Http\Middleware\AdminRouteOnly::class,
            'user.route.only'    => \App\Http\Middleware\UserRouteOnly::class,
        ]);

    // 2. تسجيل الـ ResolveCurrency ليعمل على "كل" طلبات المتجر تلقائياً
    $middleware->web(append: [
        \App\Http\Middleware\ResolveCurrency::class,
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
