<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedTinyInteger('depth')->default(0);
            $table->string('path')->nullable()->comment('Materialized path e.g. 1/3/7');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'is_active']);
            $table->index('path');
        });
    }

  public function down(): void
{
    // تعطيل القيود لضمان المسح بنجاح مهما كانت العلاقات
    Schema::disableForeignKeyConstraints();
    
    Schema::dropIfExists('categories');
    
    Schema::enableForeignKeyConstraints();
}
};