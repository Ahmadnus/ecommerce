<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the zone_delivery_schedules table.
 *
 * One row = one zone's delivery configuration for one calendar month.
 * The `available_days` column stores which days of the month are available
 * (e.g. [1,3,5,8,10,12,15] for specific dates, or null = "all days").
 * The `delivery_days` column stores the estimated lead time in days.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zone_delivery_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('zone_id')
                  ->constrained('zones')
                  ->cascadeOnDelete();

            // The month this schedule applies to — stored as "YYYY-MM"
            // e.g. "2025-07". Unique per zone per month.
            $table->string('month', 7);                    // "YYYY-MM"

            // Lead time in business days shown to the customer
            $table->unsignedSmallInteger('delivery_days')->nullable();

            // JSON array of day-of-month integers that are available for delivery
            // null means "every day in the month is available"
            // e.g. [1, 5, 10, 15, 20, 25]
            $table->json('available_days')->nullable();

            // Optional admin note (e.g. "Ramadan schedule")
            $table->string('notes', 255)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // A zone can have only one schedule per month
            $table->unique(['zone_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_delivery_schedules');
    }
};