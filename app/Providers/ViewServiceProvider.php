<?php

namespace App\Providers;

use App\Helpers\TypographySettingsHelper;
use App\Models\Currency;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * HOW TO ACTIVATE
 * ───────────────
 * If you already have a ViewServiceProvider, copy only the
 * typography composer block into your existing boot() method.
 *
 * If you don't have one, add this class to config/app.php:
 *   App\Providers\ViewServiceProvider::class
 */
class ViewServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // ── Share typography settings with every view (cached per-request) ──
        View::composer('*', function ($view) {
            static $typo = null;
            if ($typo === null) {
                $typo = TypographySettingsHelper::all();
            }
            $view->with('typoSettings', $typo);
        });

        // ── Active currency ─────────────────────────────────────────────────
        View::composer('*', function ($view) {
            static $currency = null;
            if ($currency === null) {
                $code     = session('currency', config('app.default_currency', 'USD'));
                $currency = Currency::where('code', $code)->first()
                         ?? Currency::where('is_default', true)->first()
                         ?? Currency::first();
            }
            $view->with('activeCurrency', $currency);
        });

        // ── Locale mode ─────────────────────────────────────────────────────
        View::composer('*', function ($view) {
            static $localeMode = null;
            if ($localeMode === null) {
                $localeMode = Setting::get('locale_mode', 'both');
            }
            $view->with('locale_mode', $localeMode);
        });
    }
}