<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();

            // Translatable (Spatie) → json columns
            $table->json('name');
            $table->json('description')->nullable();

            $table->string('slug')->unique();
            $table->string('icon')->nullable();          // Font Awesome class, e.g. "fa-solid fa-car"
            $table->decimal('price_from', 10, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('vehicle_categories');
        Schema::enableForeignKeyConstraints();
    }
};
