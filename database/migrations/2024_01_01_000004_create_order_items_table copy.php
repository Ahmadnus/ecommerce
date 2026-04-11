<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create order_items table
 * Line items for each order — prices are snapshotted at purchase time.
 */
return new class extends Migration
{
    public function up(): void
    {
       Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()
                  ->constrained('product_variants')->nullOnDelete();
 
            // Snapshots — preserve values even if product changes later
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('variant_name')->nullable()
                  ->comment('e.g. "أزرق / 42"');
 
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price',  10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }
 

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
