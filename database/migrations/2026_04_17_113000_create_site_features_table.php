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
            $table->string('icon', 10);        // emoji character, e.g. "🚚"
            $table->string('title', 100);       // e.g. "شحن مجاني"
            $table->string('description', 255)->nullable(); // e.g. "على طلبات فوق 50 د.أ"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed the default three features that previously were hardcoded in HTML
        DB::table('site_features')->insert([
            ['icon' => '🚚', 'title' => 'شحن مجاني',    'description' => 'على كل طلب فوق 50 د.أ',  'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => '↩️', 'title' => 'إرجاع مجاني',  'description' => 'خلال 30 يوماً من الاستلام', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['icon' => '🔒', 'title' => 'دفع آمن',       'description' => 'مشفر 100%',               'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_features');
    }
};