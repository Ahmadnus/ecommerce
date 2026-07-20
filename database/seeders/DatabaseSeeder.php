<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Fresh-install seeder for a standalone store deployment.
 *
 * Seeds only production defaults (no mock/demo data):
 *   - admin role/permissions + initial admin accounts
 *   - Jordan country record + JOD currency
 *   - the 12-governorate COD shipping matrix
 *   - default language mode + typography settings
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JordanCountrySeeder::class,
            JodCurrencySeeder::class,
            JordanZonesSeeder::class,
            LangSettingSeeder::class,
            TypographySettingsSeeder::class,
        ]);
    }
}
