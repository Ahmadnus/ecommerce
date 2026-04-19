<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * UpdateExchangeRates
 * ─────────────────────────────────────────────────────────────────────────────
 * Fetches live exchange rates from exchangerate-api.com (free tier available)
 * and updates the currencies table.
 *
 * Schedule in routes/console.php or app/Console/Kernel.php:
 *
 *   Schedule::command('currency:update-rates')->daily();
 *
 * Usage:
 *   php artisan currency:update-rates
 *   php artisan currency:update-rates --base=JOD
 *
 * Free API endpoint used (no key needed for open.er-api.com):
 *   https://open.er-api.com/v6/latest/JOD
 *
 * For production, use exchangerate-api.com with an API key for more accuracy.
 */
class UpdateExchangeRates extends Command
{
    protected $signature   = 'currency:update-rates {--base=JOD : Base currency code}';
    protected $description = 'Fetch and update exchange rates from the open exchange rate API';

    public function handle(): int
    {
        $base = strtoupper($this->option('base'));

        $this->info("Fetching rates from open.er-api.com (base: {$base})...");

        try {
            $response = Http::timeout(15)
                ->get("https://open.er-api.com/v6/latest/{$base}");

            if (! $response->successful()) {
                $this->error("API returned HTTP {$response->status()}");
                return self::FAILURE;
            }

            $data  = $response->json();
            $rates = $data['rates'] ?? [];

            if (empty($rates)) {
                $this->error('API returned empty rates. Response: ' . $response->body());
                return self::FAILURE;
            }

            // Update each currency in the DB that has a rate in the API response
            $currencies = Currency::all();
            $updated    = 0;

            foreach ($currencies as $currency) {
                if ($currency->code === $base) {
                    // Base currency always stays at 1.000000
                    $currency->update([
                        'exchange_rate' => '1.000000',
                        'is_base'       => true,
                    ]);
                    continue;
                }

                if (isset($rates[$currency->code])) {
                    $currency->update([
                        'exchange_rate' => number_format($rates[$currency->code], 6, '.', ''),
                    ]);
                    $updated++;
                    $this->line("  Updated {$currency->code}: {$rates[$currency->code]}");
                } else {
                    $this->warn("  No rate found for {$currency->code} — skipped");
                }
            }

            // Bust the CurrencyService cache so the next request gets fresh data
            Cache::forget('currencies.active');

            $this->info("Done. Updated {$updated} currencies.");
            return self::SUCCESS;

        } catch (\Exception $e) {
            Log::error('UpdateExchangeRates failed: ' . $e->getMessage());
            $this->error('Failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}