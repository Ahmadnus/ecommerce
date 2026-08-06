<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_category_id')
                  ->nullable()
                  ->constrained('vehicle_categories')
                  ->nullOnDelete();

            $table->foreignId('rental_location_id')
                  ->nullable()
                  ->constrained('rental_locations')
                  ->nullOnDelete();

            // ── Identity ────────────────────────────────────────────────────
            $table->string('brand');                 // Toyota, Hyundai, ...
            $table->string('model');                 // Camry, Sonata, ...
            $table->unsignedSmallInteger('year');
            $table->string('slug')->unique();
            $table->string('registration_number')->nullable()->unique();
            $table->string('color')->nullable();

            // Translatable marketing copy
            $table->json('description')->nullable();

            // ── Specs ───────────────────────────────────────────────────────
            $table->string('transmission')->default('automatic'); // automatic | manual
            $table->string('fuel_type')->default('petrol');       // petrol | diesel | hybrid | electric
            $table->unsignedTinyInteger('seats')->default(5);
            $table->unsignedTinyInteger('doors')->default(4);
            $table->unsignedTinyInteger('bags')->default(2);
            $table->unsignedInteger('engine_cc')->nullable();
            $table->unsignedInteger('mileage_limit_per_day')->nullable(); // km/day, null = unlimited

            // Free-form spec/feature list, e.g. ["Bluetooth","Cruise Control"]
            $table->json('features')->nullable();

            // ── Pricing ─────────────────────────────────────────────────────
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('weekly_rate', 10, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->decimal('discount_daily_rate', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);

            // ── Rental rules ────────────────────────────────────────────────
            $table->unsignedTinyInteger('min_driver_age')->default(21);
            $table->unsignedTinyInteger('min_rental_days')->default(1);

            // ── Media ───────────────────────────────────────────────────────
            $table->string('main_image')->nullable();
            $table->json('gallery_images')->nullable();

            // ── Availability ────────────────────────────────────────────────
            // available | rented | maintenance | unavailable
            $table->string('availability_status')->default('available');
            $table->unsignedInteger('units_available')->default(1);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'availability_status']);
            $table->index(['brand', 'model']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vehicles');
        Schema::enableForeignKeyConstraints();
    }
};
