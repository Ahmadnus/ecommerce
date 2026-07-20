<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Zone;
use Illuminate\Database\Seeder;

/**
 * Seeds the Jordanian COD shipping matrix: the Jordan country record plus
 * all 12 governorates with sensible default shipping prices (JOD).
 * Idempotent — safe to run on a fresh cPanel install or an existing store;
 * the owner adjusts prices from Admin → أسعار الشحن (الدفع عند التوصيل).
 *
 *   php artisan db:seed --class=JordanZonesSeeder
 */
class JordanZonesSeeder extends Seeder
{
    public const JORDAN_ZONES = [
        ['name' => 'عمان',    'shipping_price' => 2.50, 'delivery_days' => 1],
        ['name' => 'الزرقاء', 'shipping_price' => 3.00, 'delivery_days' => 2],
        ['name' => 'إربد',    'shipping_price' => 3.50, 'delivery_days' => 2],
        ['name' => 'البلقاء', 'shipping_price' => 3.50, 'delivery_days' => 2],
        ['name' => 'مادبا',   'shipping_price' => 3.50, 'delivery_days' => 2],
        ['name' => 'جرش',     'shipping_price' => 4.00, 'delivery_days' => 3],
        ['name' => 'عجلون',   'shipping_price' => 4.00, 'delivery_days' => 3],
        ['name' => 'المفرق',  'shipping_price' => 4.00, 'delivery_days' => 3],
        ['name' => 'الكرك',   'shipping_price' => 4.50, 'delivery_days' => 3],
        ['name' => 'الطفيلة', 'shipping_price' => 4.50, 'delivery_days' => 3],
        ['name' => 'معان',    'shipping_price' => 5.00, 'delivery_days' => 4],
        ['name' => 'العقبة',  'shipping_price' => 5.00, 'delivery_days' => 3],
    ];

    public function run(): void
    {
        $jordan = Country::where('code', Country::JORDAN_CODE)->first()
            ?? Country::create([
                'name'         => 'الأردن',
                'name_en'      => 'Jordan',
                'code'         => Country::JORDAN_CODE,
                'calling_code' => '+962',
                'is_active'    => true,
                'is_system'    => true,
            ]);

        foreach (self::JORDAN_ZONES as $i => $zone) {
            Zone::firstOrCreate(
                ['country_id' => $jordan->id, 'name' => $zone['name']],
                $zone + ['is_active' => true, 'sort_order' => $i + 1],
            );
        }
    }
}
