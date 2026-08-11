<?php

use App\Http\Middleware\SetLocale;
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
               'guest.checkout' => \App\Http\Middleware\GuestCheckout::class,
            // الـ Middlewares الجديدة التي طلبت إضافتها
            'admin.route.only'   => \App\Http\Middleware\AdminRouteOnly::class,
            'user.route.only'    => \App\Http\Middleware\UserRouteOnly::class,
            
              
        ]);

    // 2. تسجيل الـ ResolveCurrency ليعمل على "كل" طلبات المتجر تلقائياً
    $middleware->web(append: [
        \App\Http\Middleware\ResolveCurrency::class,
       \App\Http\Middleware\SetLocale::class
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Never show the raw "419 | Page Expired" screen.
        // A CSRF token mismatch almost always means the session cookie was lost
        // (expired tab, cached HTML, or a session store that cannot persist).
        // Bounce the user back to the same form with a fresh token instead.
        // NOTE: Laravel converts TokenMismatchException into an HttpException(419)
        // in prepareException() before render callbacks run, so match on the
        // status code rather than on TokenMismatchException itself.
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e,
            \Illuminate\Http\Request $request
        ) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            $message = __('app.session_expired_please_retry');

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 419);
            }

            return redirect()
                ->back()
                ->withInput($request->except([
                    'password', 'password_confirmation', '_token',
                ]))
                ->with('error', $message);
        });
    })->create();
