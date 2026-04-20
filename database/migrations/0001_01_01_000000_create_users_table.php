<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            // التعديل هنا: زيادة الطول لـ 20 حرفاً لاستيعاب الصيغة الدولية E.164
            $table->string('phone', 20)->unique()
                  ->comment('Primary identifier — stored in E.164 format (+963...)');
            
            $table->timestamp('phone_verified_at')->nullable();
            
            // الإيميل يبقى اختيارياً تماماً
            $table->string('email')->unique()->nullable();
            
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // الفهرس للبحث السريع
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};