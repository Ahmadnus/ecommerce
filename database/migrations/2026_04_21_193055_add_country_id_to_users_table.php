<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. إضافة الحقل بعد حقل الـ email (أو أي مكان بتفضله)
            $table->unsignedBigInteger('country_id')->nullable()->after('email');

            // 2. إضافة الـ Foreign Key
            $table->foreign('country_id')
                  ->references('id')
                  ->on('countries')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف الربط أولاً
            $table->dropForeign(['country_id']);
            // حذف الحقل ثانياً
            $table->dropColumn('country_id');
        });
    }
};