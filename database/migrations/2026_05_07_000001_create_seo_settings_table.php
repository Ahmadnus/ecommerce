<?php
// database/migrations/2026_05_07_000001_create_seo_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            // 'main' = main website layout, 'splash' = splash page
            $table->string('type')->unique()->default('main');

            // Translatable fields (JSON) — AR + EN via Spatie Translatable
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->json('og_title')->nullable();
            $table->json('og_description')->nullable();
            $table->json('twitter_title')->nullable();
            $table->json('twitter_description')->nullable();

            // Non-translatable fields
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index, follow');
            $table->string('twitter_card')->default('summary_large_image');
            $table->string('og_type')->default('website');

            // og_image and favicon handled by Spatie Media Library
            // favicon handled by Spatie Media Library

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};