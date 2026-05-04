<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $mode = session('locale_mode');

        // إذا ما في mode، استخدم النظام القديم
        if (!$mode) {
            $locale = session('locale', config('app.locale'));

            if (in_array($locale, ['ar', 'en'])) {
                app()->setLocale($locale);
            }

            view()->share('locale_mode', $locale);
            return $next($request);
        }

        // الحالات الجديدة
        if ($mode === 'ar') {
            app()->setLocale('ar');
        } elseif ($mode === 'en') {
            app()->setLocale('en');
        } else {
            // both → اختار default (مثلاً عربي)
            app()->setLocale('ar');
        }

        view()->share('locale_mode', $mode);

        return $next($request);
    }
}