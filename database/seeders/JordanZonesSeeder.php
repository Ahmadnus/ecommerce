<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * Restores the 12 Jordanian governorates as shipping zones.
 * Idempotent: matches on (country_id, name) so existing zones —
 * including admin-customized prices — are never overwritten.
 */
class JordanZonesSeeder extends Seeder
{
    public function run(): void
    {
        $jordan = Country::where('code', Country::JORDAN_CODE)->first();

        if (!$jordan) {
            $this->command?->warn('Jordan country row missing — run JordanCountrySeeder first.');
            return;
        }

        $governorates = [
            ['name' => 'عمان',    'name_en' => 'Amman',   'shipping_price' => 2.50, 'delivery_days' => 1],
            ['name' => 'الزرقاء', 'name_en' => 'Zarqa',   'shipping_price' => 3.00, 'delivery_days' => 1],
            ['name' => 'إربد',    'name_en' => 'Irbid',   'shipping_price' => 3.50, 'delivery_days' => 2],
            ['name' => 'البلقاء', 'name_en' => 'Balqa',   'shipping_price' => 3.50, 'delivery_days' => 2],
            ['name' => 'مادبا',   'name_en' => 'Madaba',  'shipping_price' => 3.50, 'delivery_days' => 2],
            ['name' => 'جرش',     'name_en' => 'Jerash',  'shipping_price' => 4.00, 'delivery_days' => 2],
            ['name' => 'عجلون',   'name_en' => 'Ajloun',  'shipping_price' => 4.00, 'delivery_days' => 2],
            ['name' => 'المفرق',  'name_en' => 'Mafraq',  'shipping_price' => 4.00, 'delivery_days' => 2],
            ['name' => 'الكرك',   'name_en' => 'Karak',   'shipping_price' => 4.50, 'delivery_days' => 3],
            ['name' => 'الطفيلة', 'name_en' => 'Tafilah', 'shipping_price' => 4.50, 'delivery_days' => 3],
            ['name' => 'معان',    'name_en' => "Ma'an",   'shipping_price' => 5.00, 'delivery_days' => 3],
            ['name' => 'العقبة',  'name_en' => 'Aqaba',   'shipping_price' => 5.00, 'delivery_days' => 3],
        ];

        foreach ($governorates as $i => $gov) {
            Zone::firstOrCreate(
                ['country_id' => $jordan->id, 'name' => $gov['name']],
                $gov + ['is_active' => true, 'sort_order' => $i + 1],
            );
        }
    }
}
