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

            // Demo build only: catalogue + orders behind the legacy store
            // dashboard, so /admin doesn't present empty tables.
            DemoStoreSeeder::class,
        ]);
    }
}
