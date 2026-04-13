<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create orders table
 * Stores customer orders with shipping info and status tracking.
 */
return new class extends Migration
{
    public function up(): void
    {
   
        // ── Orders ────────────────────────────────────────────────────────────
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
           $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number')->unique()
                  ->comment('Human-readable: ORD-YYYYMMDD-XXXXX');
 
            // Status — uses the constants defined in the Order model
            $table->string('status')->default('pending')
                  ->comment('pending|processing|shipped|delivered|cancelled');
 
            // Payment
            $table->string('payment_method')->default('cod')
                  ->comment('cod = Cash on Delivery');
            $table->string('payment_status')->default('pending')
                  ->comment('pending|paid|refunded');
 
            // Snapshot totals
            $table->decimal('subtotal',        10, 2)->default(0);
            $table->decimal('tax_amount',      10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('total_amount',    10, 2)->default(0);
 
            // Shipping info (snapshot from form)
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_zip')->nullable();
 
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
 
            $table->index(['user_id', 'status']);
            $table->index('order_number');
        });
    }

   public function down(): void
{
    Schema::disableForeignKeyConstraints(); // تعطيل القيود
    Schema::dropIfExists('orders');
    Schema::enableForeignKeyConstraints(); // إعادة تفعيل القيود
}
};
