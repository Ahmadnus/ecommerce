<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds is_system flag to countries.
     * System countries (e.g. Jordan) cannot be deleted and have
     * immutable core fields (code, calling_code).
     */
    public function up(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->boolean('is_system')
                  ->default(false)
                  ->after('is_active')
                  ->comment('System-protected records cannot be deleted or have core fields changed.');
        });
    }

    public function down(): void
    {
        Schema::table('countries', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};