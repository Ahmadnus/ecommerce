<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_locations', function (Blueprint $table) {
            $table->id();

            // Translatable (Spatie) → json columns, same convention as products/categories
            $table->json('name');
            $table->json('city');
            $table->json('address')->nullable();

            $table->string('slug')->unique();
            $table->string('code')->nullable();          // e.g. RUH-AIRPORT
            $table->string('type')->default('branch');   // branch | airport | delivery
            $table->string('phone')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // Fee applied when this branch is used as pickup, and the
            // surcharge applied when the customer returns to a different branch.
            $table->decimal('pickup_fee', 10, 2)->default(0);
            $table->decimal('one_way_fee', 10, 2)->default(0);

            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_24h')->default(false);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('rental_locations');
        Schema::enableForeignKeyConstraints();
    }
};
