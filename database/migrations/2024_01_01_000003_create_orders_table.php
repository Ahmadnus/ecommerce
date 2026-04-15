<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number')->unique();

            // الحالة - Status
            $table->string('status')->default('pending');

            // الدفع - Payment
            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending');

            // المبالغ المالية
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0); // البديل عن الضريبة
            
            // إضافة هذا الحقل ضروري لأن الكود يرسله ✅
            $table->decimal('shipping_amount', 10, 2)->default(0); 
            
            $table->decimal('total_amount', 10, 2)->default(0);

            // معلومات الشحن
            $table->string('shipping_name');
            
            // إضافة الإيميل لأن الكود يرسله في الـ Resource والـ Controller ✅
            $table->string('shipping_email')->nullable(); 
            
            $table->string('shipping_phone')->nullable();
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_zip')->nullable();
            
            // حقل إضافي للدولة إذا كان الكود يرسله
            $table->string('shipping_country')->default('EG');

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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('orders');
        Schema::enableForeignKeyConstraints();
    }
};