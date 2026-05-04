<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class JordanCountrySeeder extends Seeder
{
    public function run(): void
    {
        // Upsert Jordan — safe to run multiple times
        $jordan = Country::updateOrCreate(
            ['code' => Country::JORDAN_CODE],   // match on ISO code
            [
                'name'         => 'الأردن',
                'name_en'      => 'Jordan',
                'calling_code' => '962',
                'sort_order'   => 1,
                'is_active'    => true,
                'is_system'    => true,         // protected
            ]
        );

        // Attach JOD as the default currency if not already attached
        $jod = Currency::where('code', 'JOD')->first();

        if ($jod && !$jordan->currencies()->where('currency_id', $jod->id)->exists()) {
            $jordan->currencies()->attach($jod->id, ['is_default' => true]);
        }
    }
}