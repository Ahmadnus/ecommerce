<?php

namespace Database\Seeders;

use App\Models\RentalLocation;
use App\Models\Setting;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Database\Seeder;

/**
 * Demo content for the car-rental platform: branches, vehicle classes and a
 * starter fleet. Idempotent — safe to re-run.
 */
class CarRentalSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $locations  = $this->seedLocations();
        $categories = $this->seedCategories();
        $this->seedVehicles($categories, $locations);
    }

    private function seedSettings(): void
    {
        $defaults = [
            'rental_vat_rate'          => '15',
            'rental_currency'          => 'SAR',
            'rental_extra_cdw'         => '35',
            'rental_extra_driver'      => '25',
            'rental_extra_gps'         => '15',
            'rental_extra_child_seat'  => '20',
            'rental_min_driver_age'    => '21',
            'rental_support_phone'     => '920000000',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /** @return array<string, RentalLocation> */
    private function seedLocations(): array
    {
        // [slug, type, name, pickup_fee, one_way_fee, is_24h] — city comes from $cities below.
        $rows = [
            ['riyadh-airport', 'airport', ['en' => 'King Khalid Intl. Airport',    'ar' => 'مطار الملك خالد الدولي'],      50, 150, true],
            ['riyadh-olaya',   'branch',  ['en' => 'Olaya Branch',                 'ar' => 'فرع العليا'],                   0, 120, false],
            ['jeddah-airport', 'airport', ['en' => 'King Abdulaziz Intl. Airport', 'ar' => 'مطار الملك عبدالعزيز الدولي'], 50, 150, true],
            ['jeddah-tahlia',  'branch',  ['en' => 'Tahlia Street Branch',         'ar' => 'فرع شارع التحلية'],             0, 120, false],
            ['dammam-airport', 'airport', ['en' => 'King Fahd Intl. Airport',      'ar' => 'مطار الملك فهد الدولي'],       50, 150, true],
            ['makkah-central', 'branch',  ['en' => 'Makkah Central Branch',        'ar' => 'فرع مكة المركزي'],              0, 130, false],
            ['madinah-branch', 'branch',  ['en' => 'Madinah Branch',               'ar' => 'فرع المدينة المنورة'],          0, 130, false],
        ];

        $cities = [
            'riyadh-airport' => ['en' => 'Riyadh',  'ar' => 'الرياض'],
            'riyadh-olaya'   => ['en' => 'Riyadh',  'ar' => 'الرياض'],
            'jeddah-airport' => ['en' => 'Jeddah',  'ar' => 'جدة'],
            'jeddah-tahlia'  => ['en' => 'Jeddah',  'ar' => 'جدة'],
            'dammam-airport' => ['en' => 'Dammam',  'ar' => 'الدمام'],
            'makkah-central' => ['en' => 'Makkah',  'ar' => 'مكة المكرمة'],
            'madinah-branch' => ['en' => 'Madinah', 'ar' => 'المدينة المنورة'],
        ];

        $locations = [];
        $order     = 0;

        foreach ($rows as [$slug, $type, $name, $pickupFee, $oneWayFee, $is24h]) {
            $locations[$slug] = RentalLocation::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $name,
                    'city'        => $cities[$slug],
                    'type'        => $type,
                    'code'        => strtoupper(str_replace('-', '_', $slug)),
                    'pickup_fee'  => $pickupFee,
                    'one_way_fee' => $oneWayFee,
                    'is_24h'      => $is24h,
                    'opens_at'    => $is24h ? null : '08:00:00',
                    'closes_at'   => $is24h ? null : '22:00:00',
                    'is_active'   => true,
                    'sort_order'  => $order++,
                ]
            );
        }

        return $locations;
    }

    /** @return array<string, VehicleCategory> */
    private function seedCategories(): array
    {
        $rows = [
            ['economy', ['en' => 'Economy', 'ar' => 'اقتصادية'],  'fa-solid fa-car-side',  90],
            ['compact', ['en' => 'Compact', 'ar' => 'مدمجة'],     'fa-solid fa-car',      110],
            ['sedan',   ['en' => 'Sedan',   'ar' => 'سيدان'],      'fa-solid fa-car-rear', 150],
            ['suv',     ['en' => 'SUV',     'ar' => 'دفع رباعي'],  'fa-solid fa-truck-monster', 240],
            ['luxury',  ['en' => 'Luxury',  'ar' => 'فاخرة'],      'fa-solid fa-gem',      520],
            ['van',     ['en' => 'Van',     'ar' => 'عائلية'],     'fa-solid fa-van-shuttle', 300],
        ];

        $categories = [];
        $order      = 0;

        foreach ($rows as [$slug, $name, $icon, $from]) {
            $categories[$slug] = VehicleCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $name,
                    'icon'        => $icon,
                    'price_from'  => $from,
                    'is_active'   => true,
                    'is_featured' => true,
                    'sort_order'  => $order++,
                ]
            );
        }

        return $categories;
    }

    /**
     * @param  array<string, VehicleCategory>  $categories
     * @param  array<string, RentalLocation>   $locations
     */
    private function seedVehicles(array $categories, array $locations): void
    {
        $fleet = [
            // [brand, model, year, category, seats, bags, transmission, fuel, daily, discount, units, featured]
            ['Toyota',    'Yaris',       2024, 'economy', 5, 2, 'automatic', 'petrol',   99,  89,  6, true],
            ['Hyundai',   'Accent',      2024, 'economy', 5, 2, 'automatic', 'petrol',  105, null, 5, false],
            ['Kia',       'Pegas',       2023, 'economy', 5, 2, 'manual',    'petrol',   89, null, 4, false],
            ['Nissan',    'Sunny',       2024, 'compact', 5, 3, 'automatic', 'petrol',  120, null, 5, false],
            ['Hyundai',   'Elantra',     2024, 'compact', 5, 3, 'automatic', 'petrol',  135,  125, 6, true],
            ['Toyota',    'Camry',       2024, 'sedan',   5, 4, 'automatic', 'hybrid',  185, null, 4, true],
            ['Honda',     'Accord',      2023, 'sedan',   5, 4, 'automatic', 'petrol',  175, null, 3, false],
            ['Toyota',    'Land Cruiser',2024, 'suv',     7, 5, 'automatic', 'petrol',  480,  440, 3, true],
            ['Nissan',    'Patrol',      2024, 'suv',     7, 5, 'automatic', 'petrol',  460, null, 2, false],
            ['Hyundai',   'Tucson',      2024, 'suv',     5, 4, 'automatic', 'petrol',  245, null, 4, false],
            ['Mercedes',  'E-Class',     2024, 'luxury',  5, 4, 'automatic', 'petrol',  650, null, 2, true],
            ['BMW',       '5 Series',    2024, 'luxury',  5, 4, 'automatic', 'petrol',  620,  580, 2, false],
            ['Toyota',    'Hiace',       2023, 'van',    11, 8, 'manual',    'diesel',  320, null, 3, false],
            ['Hyundai',   'Staria',      2024, 'van',     9, 6, 'automatic', 'diesel',  340, null, 2, true],
        ];

        $featureSets = [
            'economy' => ['Bluetooth', 'USB Charging', 'Air Conditioning'],
            'compact' => ['Bluetooth', 'Rear Camera', 'Cruise Control', 'Air Conditioning'],
            'sedan'   => ['Bluetooth', 'Rear Camera', 'Cruise Control', 'Leather Seats', 'Sunroof'],
            'suv'     => ['Bluetooth', '360 Camera', 'Cruise Control', 'Leather Seats', '4WD', 'Roof Rails'],
            'luxury'  => ['Bluetooth', '360 Camera', 'Adaptive Cruise', 'Leather Seats', 'Sunroof', 'Ambient Lighting'],
            'van'     => ['Bluetooth', 'Rear Camera', 'Air Conditioning', 'Large Luggage Space'],
        ];

        $locationKeys = array_keys($locations);
        $order        = 0;

        foreach ($fleet as [$brand, $model, $year, $catSlug, $seats, $bags, $transmission, $fuel, $daily, $discount, $units, $featured]) {
            $slug = \Illuminate\Support\Str::slug("{$brand}-{$model}-{$year}");

            Vehicle::updateOrCreate(
                ['slug' => $slug],
                [
                    'vehicle_category_id'   => $categories[$catSlug]->id,
                    'rental_location_id'    => $locations[$locationKeys[$order % count($locationKeys)]]->id,
                    'brand'                 => $brand,
                    'model'                 => $model,
                    'year'                  => $year,
                    'seats'                 => $seats,
                    'doors'                 => $seats > 7 ? 5 : 4,
                    'bags'                  => $bags,
                    'transmission'          => $transmission,
                    'fuel_type'             => $fuel,
                    'mileage_limit_per_day' => 250,
                    'features'              => $featureSets[$catSlug],
                    'daily_rate'            => $daily,
                    'discount_daily_rate'   => $discount,
                    'weekly_rate'           => round($daily * 6.3, 2),
                    'monthly_rate'          => round($daily * 24, 2),
                    'security_deposit'      => $daily * 5,
                    'min_driver_age'        => in_array($catSlug, ['luxury', 'suv'], true) ? 25 : 21,
                    'min_rental_days'       => 1,
                    'availability_status'   => Vehicle::STATUS_AVAILABLE,
                    'units_available'       => $units,
                    'is_active'             => true,
                    'is_featured'           => $featured,
                    'sort_order'            => $order++,
                ]
            );
        }
    }
}
