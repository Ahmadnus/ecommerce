<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Mock root categories to test the 2-column square grid layout.
     * Each entry downloads its Unsplash image into the category_images
     * media collection so getCategoryImageUrl('thumb') returns a real photo.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'  => ["en" => "Women's Ready to Wear", "ar" => "ملابس نسائية جاهزة"],
                'slug'  => 'womens-ready-to-wear',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&q=80',
            ],
            [
                'name'  => ["en" => "Women's Shoes", "ar" => "أحذية نسائية"],
                'slug'  => 'womens-shoes',
                'image' => 'https://images.unsplash.com/photo-1518049362265-d5b2a6467637?w=800&q=80',
            ],
            [
                'name'  => ["en" => "Hats", "ar" => "قبعات"],
                'slug'  => 'hats',
                'image' => 'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=800&q=80',
            ],
            [
                'name'  => ["en" => "Beach", "ar" => "شاطئ"],
                'slug'  => 'beach',
                'image' => 'https://images.unsplash.com/photo-1509233725247-49e657c54213?w=800&q=80',
            ],
            [
                'name'  => ["en" => "Travel", "ar" => "سفر"],
                'slug'  => 'travel',
                'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80',
            ],
            [
                'name'  => ["en" => "Accessories", "ar" => "اكسسوارات"],
                'slug'  => 'accessories',
                'image' => 'https://images.unsplash.com/photo-1512909006721-3d6018887383?w=800&q=80',
            ],
        ];

        foreach ($categories as $index => $data) {
            $category = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name'       => $data['name'],
                    'is_active'  => true,
                    'sort_order' => $index,
                ]
            );

            $category->clearMediaCollection('category_images');
            $category->addMediaFromUrl($data['image'])
                ->toMediaCollection('category_images');
        }

        $this->command->info('Test categories seeded with images for the grid layout.');
    }
}
