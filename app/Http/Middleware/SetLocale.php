<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $globalMode = cache()->remember('langsetting', 3600, function () {
            return DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';
        });

        if ($globalMode === 'ar') {
            app()->setLocale('ar');
            session(['locale' => 'ar']);
        } elseif ($globalMode === 'en') {
            app()->setLocale('en');
            session(['locale' => 'en']);
        } else {
            // 'both' → respect user session choice
            $locale = session('locale', config('app.locale', 'ar'));
            if (in_array($locale, ['ar', 'en'])) {
                app()->setLocale($locale);
            }
        }

        view()->share('locale_mode', $globalMode);
        view()->share('isRtl', app()->getLocale() === 'ar');

        return $next($request);
    }
}