<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JordanCountrySeeder::class,
            JodCurrencySeeder::class,
            LangSettingSeeder::class,
            TypographySettingsSeeder::class,
            CarRentalSeeder::class,
            VehiclePhotoSeeder::class,
            // Branding, logo, contact links, hero slides, pages and SEO.
            // Runs last so it can overwrite the e-commerce defaults that some
            // of the older migrations insert inline.
            SiteContentSeeder::class,
        ]);
    }
}
