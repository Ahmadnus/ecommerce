<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * home_sections table
     * ────────────────────────────────────────────────────────────────────
     * Each row is one visible block on the homepage.
     * The `type` column determines where products come from:
     *
     *   featured        → is_featured = true
     *   latest          → newest first
     *   price_high      → base_price DESC
     *   price_low       → base_price ASC
     *   category        → products in category_id
     *
     * `limit` caps how many products to fetch (horizontal list).
     * `sort_order` controls the display order of the sections themselves.
     */
   public function up(): void
{
    Schema::create('home_sections', function (Blueprint $table) {
        $table->id();
        $table->json('title');              // ← was string('title', 120)
        $table->enum('type', [
            'featured',
            'latest',
            'price_high',
            'price_low',
            'category',
        ])->default('featured');
        $table->foreignId('category_id')
              ->nullable()
              ->constrained('categories')
              ->nullOnDelete();
        $table->unsignedTinyInteger('limit')->default(10);
        $table->unsignedSmallInteger('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    DB::table('home_sections')->insert([
        [
            'title'       => json_encode(['ar' => 'المنتجات المميزة', 'en' => 'Featured Products']),
            'type'        => 'featured',
            'category_id' => null,
            'limit'       => 10,
            'sort_order'  => 1,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ],
        [
            'title'       => json_encode(['ar' => 'أحدث المنتجات', 'en' => 'Latest Products']),
            'type'        => 'latest',
            'category_id' => null,
            'limit'       => 10,
            'sort_order'  => 2,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ],
        [
            'title'       => json_encode(['ar' => 'عروض الأسعار', 'en' => 'Price Deals']),
            'type'        => 'price_low',
            'category_id' => null,
            'limit'       => 10,
            'sort_order'  => 3,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ],
    ]);
}
    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};