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
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict'); // Prevent deleting ordered products
            $table->string('product_name');             // Snapshot product name at order time
            $table->string('product_sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);       // Price at time of purchase (not current price)
            $table->decimal('total_price', 10, 2);      // quantity * unit_price
            $table->json('options')->nullable();         // e.g., size, color variants
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
