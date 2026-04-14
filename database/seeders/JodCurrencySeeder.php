<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

/**
 * Seeds the Jordanian Dinar (JOD) as the base currency.
 * Run: php artisan db:seed --class=JodCurrencySeeder
 *
 * Also resets any previously marked base currency so JOD is the only one.
 */
class JodCurrencySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing base flag
        Currency::where('is_base', true)->update(['is_base' => false]);

        Currency::updateOrCreate(
            ['code' => 'JOD'],
            [
                'name'          => 'دينار أردني',
                'symbol'        => 'د.أ',
                'exchange_rate' => 1.000000,   // JOD IS the base — rate always 1
                'is_base'       => true,
                'is_active'     => true,
            ]
        );

        $this->command->info('✓ JOD set as base currency (rate = 1.000000)');

        // Optionally seed a few common currencies relative to JOD
        $others = [
            ['code' => 'USD', 'name' => 'دولار أمريكي',    'symbol' => '$',   'rate' => 0.7067],
            ['code' => 'EUR', 'name' => 'يورو',             'symbol' => '€',   'rate' => 0.6520],
            ['code' => 'SAR', 'name' => 'ريال سعودي',      'symbol' => '﷼',   'rate' => 2.6501],
            ['code' => 'AED', 'name' => 'درهم إماراتي',    'symbol' => 'د.إ', 'rate' => 2.5951],
            ['code' => 'SYP', 'name' => 'ليرة سورية',      'symbol' => 'ل.س', 'rate' => 9182.00],
            ['code' => 'EGP', 'name' => 'جنيه مصري',       'symbol' => 'ج.م', 'rate' => 34.50],
        ];

        foreach ($others as $data) {
            Currency::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name'          => $data['name'],
                    'symbol'        => $data['symbol'],
                    'exchange_rate' => $data['rate'],
                    'is_base'       => false,
                    'is_active'     => true,
                ]
            );
            $this->command->line("  + {$data['code']} ({$data['symbol']}) rate = {$data['rate']}");
        }

        $this->command->info('✓ Common regional currencies seeded.');
    }
}