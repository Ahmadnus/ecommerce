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
            // Jordan: 16% general sales tax, prices in Jordanian Dinar.
            'rental_vat_rate'          => '16',
            'rental_currency'          => 'JOD',
            'rental_extra_cdw'         => '8',
            'rental_extra_driver'      => '6',
            'rental_extra_gps'         => '4',
            'rental_extra_child_seat'  => '5',
            'rental_min_driver_age'    => '21',
            'rental_support_phone'     => '+962 6 500 0000',
            'rental_support_email'     => 'info@keyrental.jo',
            'rental_support_address'   => 'عمان - شارع مكة، المملكة الأردنية الهاشمية',
        ];

        // These carry Saudi values on older installs, so they are corrected
        // rather than merely defaulted.
        $overrides = [
            'rental_currency', 'rental_vat_rate', 'rental_support_phone',
            'rental_extra_cdw', 'rental_extra_driver', 'rental_extra_gps', 'rental_extra_child_seat',
        ];

        foreach ($defaults as $key => $value) {
            if (in_array($key, $overrides, true)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);

                continue;
            }

            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    /** @return array<string, RentalLocation> */
    private function seedLocations(): array
    {
        /*
         * Jordan branch network. Order matters: the first row is the primary
         * branch the booking form falls back to when the URL carries no
         * location, so Queen Alia stays at the top.
         *
         * [slug, type, name, pickup_fee, one_way_fee, is_24h] — city from $cities.
         */
        $rows = [
            ['amman-queen-alia', 'airport', ['en' => 'Queen Alia International Airport', 'ar' => 'مطار الملكة علياء الدولي'], 10, 25, true],
            ['amman-mecca-st',   'branch',  ['en' => 'Mecca Street / Downtown',          'ar' => 'شارع مكة / وسط البلد'],      0, 20, false],
            ['amman-abdali',     'branch',  ['en' => 'Abdali Boulevard',                 'ar' => 'العبدلي'],                    0, 20, false],
            ['irbid-center',     'branch',  ['en' => 'City Center / University Street',  'ar' => 'شارع الجامعة / وسط المدينة'], 0, 30, false],
            ['aqaba-airport',    'airport', ['en' => 'King Hussein International Airport','ar' => 'مطار الملك حسين الدولي'],    10, 35, true],
            ['aqaba-center',     'branch',  ['en' => 'City Center / Beachfront',         'ar' => 'وسط المدينة / الواجهة البحرية'], 0, 35, false],
            ['zarqa-center',     'branch',  ['en' => 'City Center',                      'ar' => 'وسط المدينة'],                0, 20, false],
        ];

        $cities = [
            'amman-queen-alia' => ['en' => 'Amman', 'ar' => 'عمان'],
            'amman-mecca-st'   => ['en' => 'Amman', 'ar' => 'عمان'],
            'amman-abdali'     => ['en' => 'Amman', 'ar' => 'عمان'],
            'irbid-center'     => ['en' => 'Irbid', 'ar' => 'إربد'],
            'aqaba-airport'    => ['en' => 'Aqaba', 'ar' => 'العقبة'],
            'aqaba-center'     => ['en' => 'Aqaba', 'ar' => 'العقبة'],
            'zarqa-center'     => ['en' => 'Zarqa', 'ar' => 'الزرقاء'],
        ];

        /*
         * Retire any branch that isn't part of the Jordan network — deactivated
         * rather than deleted, because existing bookings still point at them by
         * foreign key. Deactivated rows drop out of every storefront dropdown.
         */
        RentalLocation::whereNotIn('slug', array_column($rows, 0))
            ->update(['is_active' => false]);

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
            // "From" prices in JOD, in line with the Jordanian market.
            ['economy', ['en' => 'Economy', 'ar' => 'اقتصادية'],  'fa-solid fa-car-side',  18],
            ['compact', ['en' => 'Compact', 'ar' => 'مدمجة'],     'fa-solid fa-car',       22],
            ['sedan',   ['en' => 'Sedan',   'ar' => 'سيدان'],      'fa-solid fa-car-rear', 30],
            ['suv',     ['en' => 'SUV',     'ar' => 'دفع رباعي'],  'fa-solid fa-truck-monster', 48],
            ['luxury',  ['en' => 'Luxury',  'ar' => 'فاخرة'],      'fa-solid fa-gem',     105],
            ['van',     ['en' => 'Van',     'ar' => 'عائلية'],     'fa-solid fa-van-shuttle', 60],
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
            // Daily rates are in JOD.
            ['Toyota',    'Yaris',       2024, 'economy', 5, 2, 'automatic', 'petrol',   20,  18,  6, true],
            ['Hyundai',   'Accent',      2024, 'economy', 5, 2, 'automatic', 'petrol',   21, null, 5, false],
            ['Kia',       'Pegas',       2023, 'economy', 5, 2, 'manual',    'petrol',   18, null, 4, false],
            ['Nissan',    'Sunny',       2024, 'compact', 5, 3, 'automatic', 'petrol',   24, null, 5, false],
            ['Hyundai',   'Elantra',     2024, 'compact', 5, 3, 'automatic', 'petrol',   27,  25, 6, true],
            ['Toyota',    'Camry',       2024, 'sedan',   5, 4, 'automatic', 'hybrid',   37, null, 4, true],
            ['Honda',     'Accord',      2023, 'sedan',   5, 4, 'automatic', 'petrol',   35, null, 3, false],
            ['Toyota',    'Land Cruiser',2024, 'suv',     7, 5, 'automatic', 'petrol',   96,  88, 3, true],
            ['Nissan',    'Patrol',      2024, 'suv',     7, 5, 'automatic', 'petrol',   92, null, 2, false],
            ['Hyundai',   'Tucson',      2024, 'suv',     5, 4, 'automatic', 'petrol',   49, null, 4, false],
            ['Mercedes',  'E-Class',     2024, 'luxury',  5, 4, 'automatic', 'petrol',  130, null, 2, true],
            ['BMW',       '5 Series',    2024, 'luxury',  5, 4, 'automatic', 'petrol',  124, 116, 2, false],
            ['Toyota',    'Hiace',       2023, 'van',    11, 8, 'manual',    'diesel',   64, null, 3, false],
            ['Hyundai',   'Staria',      2024, 'van',     9, 6, 'automatic', 'diesel',   68, null, 2, true],
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
