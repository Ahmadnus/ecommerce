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
            
            // الهاتف هو المعرف الأساسي
            $table->string('phone', 20)->unique()->index()->nullable();
            
            // حقول الـ OTP والتحقق
            $table->string('otp', 10)->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            // الإيميل اختياري
            $table->string('email')->unique()->nullable();
            
            // الربط مع الدول - تأكد أن جدول countries موجود
       

            $table->string('password');
            
            // حقل الأدمن للعزل الصارم
            $table->boolean('is_admin')->default(false); 
            
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};