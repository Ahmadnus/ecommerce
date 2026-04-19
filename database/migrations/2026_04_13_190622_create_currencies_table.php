<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إنشاء جدول العملات
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
        });

        // 2. إدخال البيانات الأولية
        DB::table('currencies')->insert([
            [
                'name'          => 'Jordanian Dinar',
                'code'          => 'JOD',
                'symbol'        => 'د.أ',
                'exchange_rate' => 1.000000,
                'is_base'       => true,
                'is_active'     => true,
                'sort_order'    => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'US Dollar',
                'code'          => 'USD',
                'symbol'        => '$',
                'exchange_rate' => 1.410000,
                'is_base'       => false,
                'is_active'     => true,
                'sort_order'    => 2,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Euro',
                'code'          => 'EUR',
                'symbol'        => '€',
                'exchange_rate' => 1.300000,
                'is_base'       => false,
                'is_active'     => true,
                'sort_order'    => 3,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'British Pound',
                'code'          => 'GBP',
                'symbol'        => '£',
                'exchange_rate' => 1.110000,
                'is_base'       => false,
                'is_active'     => true,
                'sort_order'    => 4,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Saudi Riyal',
                'code'          => 'SAR',
                'symbol'        => 'ر.س',
                'exchange_rate' => 5.290000,
                'is_base'       => false,
                'is_active'     => true,
                'sort_order'    => 5,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'UAE Dirham',
                'code'          => 'AED',
                'symbol'        => 'د.إ',
                'exchange_rate' => 5.180000,
                'is_base'       => false,
                'is_active'     => true,
                'sort_order'    => 6,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // 3. إنشاء جدول الربط بين الدول والعملات
        // تأكد أن جدول 'countries' موجود مسبقاً قبل تشغيل هذه الميجريشن
        Schema::create('country_currency', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->primary(['country_id', 'currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_currency');
        Schema::dropIfExists('currencies');
    }
};