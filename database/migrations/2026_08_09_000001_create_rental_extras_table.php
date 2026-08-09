<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optional booking add-ons (CDW, extra driver, GPS, child seat …).
 *
 * These used to be a hardcoded array in RentalPricingService with only the
 * prices pulled from `settings`, so the admin could change what an add-on cost
 * but not rename one, and could never add or retire one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_extras', function (Blueprint $table) {
            $table->id();

            /*
             * Stable identifier. Bookings store the code inside their `extras`
             * JSON, so renaming an add-on must not break historical records —
             * that is why the code is separate from the display name.
             */
            $table->string('code', 60)->unique();

            // Translatable {"en": "...", "ar": "..."} — handled by Spatie.
            $table->json('name');

            // Long enough for any Font Awesome class; the 10-char icon column
            // on site_features is the cautionary tale here.
            $table->string('icon', 100)->default('fa-solid fa-plus');

            $table->decimal('price', 10, 2)->default(0);

            // false = charged once per booking rather than per rental day.
            $table->boolean('per_day')->default(true);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_extras');
    }
};
