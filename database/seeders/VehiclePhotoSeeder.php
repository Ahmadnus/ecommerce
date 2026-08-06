<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

/**
 * Attaches the bundled stock photos in database/seeders/photos to the demo
 * fleet. They live beside the seeder rather than under storage/app because
 * Laravel gitignores storage/app/* — putting them there would leave a fresh
 * clone with a seeder and no images.
 *
 * These are free-licence Unsplash photos used as placeholders — they are
 * representative of each class, NOT photographs of the actual model/year on
 * the listing. Replace them with real fleet photography before going live
 * (admin → أسطول السيارات → تعديل → الصور).
 *
 * Idempotent: re-running replaces the main image rather than stacking copies.
 */
class VehiclePhotoSeeder extends Seeder
{
    /** vehicle slug => [main image, ...gallery images] */
    private const PHOTOS = [
        // ── Economy ─────────────────────────────────────────────────────────
        'toyota-yaris-2024'   => ['p-1549317661-bd32c8ce0db2.jpg'],
        'hyundai-accent-2024' => ['p-1541899481282-d53bffe3c35d.jpg'],
        'kia-pegas-2023'      => ['p-1550355291-bbee04a92027.jpg'],

        // ── Compact ─────────────────────────────────────────────────────────
        'nissan-sunny-2024'   => ['p-1502877338535-766e1452684a.jpg'],
        'hyundai-elantra-2024' => ['q-1571127236794-81c0bbfe1ce3.jpg'],

        // ── Sedan ───────────────────────────────────────────────────────────
        'toyota-camry-2024'   => ['q-1606664515524-ed2f786a0bd6.jpg'],
        'honda-accord-2023'   => ['p-1580273916550-e323be2ae537.jpg'],

        // ── SUV ─────────────────────────────────────────────────────────────
        'toyota-land-cruiser-2024' => ['p-1533473359331-0135ef1b58bf.jpg'],
        'nissan-patrol-2024'       => ['q-1519641471654-76ce0107ad1b.jpg'],
        'hyundai-tucson-2024'      => ['q-1609521263047-f8f205293f24.jpg'],

        // ── Luxury (extra gallery shots from the leftover exotics) ──────────
        'mercedes-e-class-2024' => [
            'p-1605559424843-9e4c228bf1c2.jpg',
            'q-1617788138017-80ad40651399.jpg',
            'q-1594502184342-2e12f877aa73.jpg',
        ],
        'bmw-5-series-2024' => [
            'p-1503376780353-7e6692767b70.jpg',
            'p-1494976388531-d1058494cdd8.jpg',
            'p-1552519507-da3b142c6e3d.jpg',
        ],

        // ── Van — only one van/coach photo was available, so both share it ──
        'toyota-hiace-2023'  => ['q-1570125909232-eb263c188f7e.jpg'],
        'hyundai-staria-2024' => ['q-1570125909232-eb263c188f7e.jpg'],
    ];

    public function run(): void
    {
        $sourceDir = database_path('seeders/photos');

        if (! is_dir($sourceDir)) {
            $this->command?->warn("Photo source directory missing: {$sourceDir} — skipping.");

            return;
        }

        $attached = 0;
        $missing  = [];

        foreach (self::PHOTOS as $slug => $files) {
            $vehicle = Vehicle::where('slug', $slug)->first();

            if (! $vehicle) {
                $missing[] = $slug;
                continue;
            }

            // Re-running should replace, not accumulate.
            $vehicle->clearMediaCollection('vehicle_main');
            $vehicle->clearMediaCollection('vehicle_gallery');

            foreach (array_values($files) as $index => $file) {
                $path = $sourceDir . DIRECTORY_SEPARATOR . $file;

                if (! is_file($path)) {
                    $missing[] = "{$slug}: {$file}";
                    continue;
                }

                $this->attach(
                    $vehicle,
                    $path,
                    $index === 0 ? 'vehicle_main' : 'vehicle_gallery'
                );

                $attached++;
            }
        }

        $this->command?->info("Attached {$attached} vehicle photo(s).");

        foreach ($missing as $item) {
            $this->command?->warn("Skipped: {$item}");
        }
    }

    /**
     * Convert to webp at a sane size before storing, matching what
     * Vehicle::addCompressedMedia() does for admin uploads.
     */
    private function attach(Vehicle $vehicle, string $path, string $collection): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'seed_car_');

        if ($temp && is_file($temp)) {
            @unlink($temp);
        }

        $temp .= '.webp';

        Image::load($path)
            ->fit(Fit::Contain, 1600, 1600)
            ->optimize()
            ->format('webp')
            ->save($temp);

        try {
            $vehicle->addMedia($temp)
                ->usingFileName(Str::uuid() . '.webp')
                ->toMediaCollection($collection);
        } finally {
            if (is_file($temp)) {
                @unlink($temp);
            }
        }
    }
}
