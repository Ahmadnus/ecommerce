<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create products table
 * Core product catalog with pricing, inventory, and media.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();   // Optional discounted price
            $table->integer('stock_quantity')->default(0);
            $table->string('sku')->unique()->nullable();         // Stock Keeping Unit
            $table->string('image')->nullable();                 // Main product image
            $table->json('images')->nullable();                  // Additional images array
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('weight', 8, 2)->nullable();         // For shipping calculations
            $table->timestamps();
            $table->softDeletes();                               // Soft delete for data safety

            // Indexes for common query patterns
            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
