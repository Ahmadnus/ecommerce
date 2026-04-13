<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->string('name');                          // اسم المنطقة / المدينة
            $table->decimal('shipping_price', 10, 2)->default(0.00);
            $table->unsignedInteger('delivery_days')->nullable()
                  ->comment('Estimated delivery in days');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['country_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};