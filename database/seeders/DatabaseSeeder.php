<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DatabaseSeeder
 *
 * Seeds sample categories and products for development.
 * Run with: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a demo user
        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name'     => 'Demo User',
                'password' => bcrypt('password'),
            ]
        );

        // ── Categories ──────────────────────────────────────────────────────
        $categories = [
            ['name' => 'Electronics',   'slug' => 'electronics',   'description' => 'Gadgets, devices and accessories'],
            ['name' => 'Clothing',       'slug' => 'clothing',       'description' => 'Fashion for every occasion'],
            ['name' => 'Home & Garden',  'slug' => 'home-garden',    'description' => 'Everything for your home'],
            ['name' => 'Sports',         'slug' => 'sports',         'description' => 'Gear for every sport'],
            ['name' => 'Books',          'slug' => 'books',          'description' => 'Explore new worlds'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }

        // ── Products ─────────────────────────────────────────────────────────
        $products = [
            // Electronics
            ['category' => 'electronics', 'name' => 'Wireless Noise-Cancelling Headphones', 'price' => 149.99, 'sale_price' => 119.99, 'stock' => 42, 'featured' => true,
             'desc' => 'Premium wireless headphones with active noise cancellation, 30-hour battery life, and ultra-comfortable ear cushions. Perfect for travel, work, or relaxing at home.'],
            ['category' => 'electronics', 'name' => 'Smart Watch Series X', 'price' => 299.99, 'sale_price' => null, 'stock' => 18, 'featured' => true,
             'desc' => 'Track your fitness, receive notifications, and monitor your health with this sleek smartwatch. Features GPS, heart rate monitor, and 5-day battery.'],
            ['category' => 'electronics', 'name' => 'Portable Bluetooth Speaker', 'price' => 79.99, 'sale_price' => 59.99, 'stock' => 65, 'featured' => false,
             'desc' => 'Compact, waterproof speaker with 360° sound and 12-hour playtime. Perfect for outdoor adventures.'],
            ['category' => 'electronics', 'name' => 'USB-C Charging Hub 7-in-1', 'price' => 49.99, 'sale_price' => null, 'stock' => 93, 'featured' => false,
             'desc' => 'Expand your laptop connectivity with HDMI 4K, USB-A, SD card, and Ethernet ports.'],

            // Clothing
            ['category' => 'clothing', 'name' => 'Classic Oxford Button-Down Shirt', 'price' => 59.99, 'sale_price' => null, 'stock' => 120, 'featured' => true,
             'desc' => 'Timeless oxford shirt crafted from 100% premium cotton. Slim fit with a clean, versatile look for work or weekend wear.'],
            ['category' => 'clothing', 'name' => 'Premium Merino Wool Sweater', 'price' => 129.99, 'sale_price' => 89.99, 'stock' => 34, 'featured' => false,
             'desc' => 'Luxuriously soft merino wool sweater with a relaxed fit. Naturally temperature-regulating and itch-free.'],
            ['category' => 'clothing', 'name' => 'Performance Running Jacket', 'price' => 89.99, 'sale_price' => null, 'stock' => 56, 'featured' => true,
             'desc' => 'Lightweight, windproof jacket with moisture-wicking technology. Reflective details for safety in low-light conditions.'],

            // Home & Garden
            ['category' => 'home-garden', 'name' => 'Ceramic Pour-Over Coffee Set', 'price' => 44.99, 'sale_price' => null, 'stock' => 78, 'featured' => true,
             'desc' => 'Hand-crafted ceramic pour-over dripper with matching mug. Includes reusable stainless filter. Elevate your morning ritual.'],
            ['category' => 'home-garden', 'name' => 'Bamboo Cutting Board Set (3-piece)', 'price' => 34.99, 'sale_price' => 24.99, 'stock' => 101, 'featured' => false,
             'desc' => 'Eco-friendly bamboo cutting boards in three sizes. Naturally antimicrobial and easy on knife edges.'],
            ['category' => 'home-garden', 'name' => 'Scented Soy Wax Candle Collection', 'price' => 39.99, 'sale_price' => null, 'stock' => 155, 'featured' => false,
             'desc' => 'Set of 3 hand-poured soy candles in calming scents: Lavender & Cedar, Vanilla Bean, and Sea Salt & Sage.'],

            // Sports
            ['category' => 'sports', 'name' => 'Adjustable Dumbbell Set (5–52.5 lbs)', 'price' => 349.99, 'sale_price' => 299.99, 'stock' => 12, 'featured' => true,
             'desc' => 'Replace 15 sets of weights with one compact, adjustable system. Dial in your weight in seconds from 5 to 52.5 lbs.'],
            ['category' => 'sports', 'name' => 'Yoga Mat — 6mm Premium', 'price' => 49.99, 'sale_price' => null, 'stock' => 88, 'featured' => false,
             'desc' => 'Non-slip, eco-friendly TPE yoga mat with alignment lines. Includes carrying strap.'],

            // Books
            ['category' => 'books', 'name' => 'The Art of Clean Code', 'price' => 29.99, 'sale_price' => null, 'stock' => 200, 'featured' => false,
             'desc' => 'A practical guide to writing maintainable, scalable software. Covers design patterns, refactoring, and clean architecture principles.'],
            ['category' => 'books', 'name' => 'Deep Work: Rules for Success', 'price' => 19.99, 'sale_price' => 14.99, 'stock' => 175, 'featured' => false,
             'desc' => 'Discover the rare ability to focus without distraction on cognitively demanding tasks, and why mastering it is key to professional success.'],
        ];

        $categoryMap = Category::pluck('id', 'slug');

        foreach ($products as $p) {
            $name = $p['name'];
            Product::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id'       => $categoryMap[$p['category']],
                    'name'              => $name,
                    'slug'              => Str::slug($name),
                    'description'       => $p['desc'],
                    'short_description' => Str::limit($p['desc'], 80),
                    'price'             => $p['price'],
                    'sale_price'        => $p['sale_price'],
                    'stock_quantity'    => $p['stock'],
                    'sku'               => strtoupper(Str::random(8)),
                    // Using placeholder images from picsum — replace with real assets
                    'image'             => 'https://picsum.photos/seed/' . Str::slug($name) . '/600/600',
                    'is_active'         => true,
                    'is_featured'       => $p['featured'],
                ]
            );
        }

        $this->command->info('✅ Seeded ' . count($products) . ' products across ' . count($categories) . ' categories.');
    }
}
