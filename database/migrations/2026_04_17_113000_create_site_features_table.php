<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_features', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 10);
            $table->json('title');                    // {"en": "Free Shipping", "ar": "شحن مجاني"}
            $table->json('description')->nullable();  // {"en": "On orders above $50", "ar": "على طلبات فوق 50 د.أ"}
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('site_features')->insert([
            [
                'icon'        => '🚚',
                'title'       => json_encode(['ar' => 'شحن مجاني',   'en' => 'Free Shipping']),
                'description' => json_encode(['ar' => 'على كل طلب فوق 50 د.أ', 'en' => 'On all orders above $50']),
                'sort_order'  => 1,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'icon'        => '↩️',
                'title'       => json_encode(['ar' => 'إرجاع مجاني', 'en' => 'Free Returns']),
                'description' => json_encode(['ar' => 'خلال 30 يوماً من الاستلام', 'en' => 'Within 30 days of receipt']),
                'sort_order'  => 2,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'icon'        => '🔒',
                'title'       => json_encode(['ar' => 'دفع آمن',    'en' => 'Secure Payment']),
                'description' => json_encode(['ar' => 'مشفر 100%',  'en' => '100% encrypted']),
                'sort_order'  => 3,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_features');
    }
};