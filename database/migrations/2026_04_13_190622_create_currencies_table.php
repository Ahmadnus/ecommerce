<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->string('symbol', 10);
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('country_currency', function (Blueprint $table) {
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_default')->default(false);
            $table->primary(['country_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_currency');
        Schema::dropIfExists('currencies');
    }
};