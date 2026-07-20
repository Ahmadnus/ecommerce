<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Order financial consolidation:
 *  - currency_code          : the Main Store Currency every amount on the row
 *                             is denominated in (snapshot at order time)
 *  - display_currency_code  : the currency the customer was browsing in
 *  - display_exchange_rate  : that currency's rate at order time (reference
 *                             only — amounts are NEVER stored converted)
 *  - zone_id / shipping_area / delivery_days : the checkout already sent
 *    these, but the columns never existed so the data was silently dropped.
 *
 * Also repairs delivery_fee for past orders (total - subtotal) — checkout
 * used to write the fee to a non-existent tax_amount column.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'zone_id')) {
                $table->foreignId('zone_id')->nullable()->after('payment_status')
                    ->constrained('zones')->nullOnDelete();
            }
            if (! Schema::hasColumn('orders', 'shipping_area')) {
                $table->string('shipping_area')->nullable()->after('zone_id');
            }
            if (! Schema::hasColumn('orders', 'delivery_days')) {
                $table->unsignedSmallInteger('delivery_days')->nullable()->after('shipping_area');
            }
            if (! Schema::hasColumn('orders', 'currency_code')) {
                $table->string('currency_code', 10)->nullable()->after('total_amount');
            }
            if (! Schema::hasColumn('orders', 'display_currency_code')) {
                $table->string('display_currency_code', 10)->nullable()->after('currency_code');
            }
            if (! Schema::hasColumn('orders', 'display_exchange_rate')) {
                $table->decimal('display_exchange_rate', 12, 6)->nullable()->after('display_currency_code');
            }
        });

        // Backfill: stamp historical orders with the current Main Store
        // Currency and repair the lost delivery fee.
        $baseCode = DB::table('currencies')->where('is_base', true)->value('code') ?? 'JOD';

        DB::table('orders')->whereNull('currency_code')->update(['currency_code' => $baseCode]);

        DB::table('orders')
            ->where(fn ($q) => $q->whereNull('delivery_fee')->orWhere('delivery_fee', 0))
            ->whereColumn('total_amount', '>', 'subtotal')
            ->update(['delivery_fee' => DB::raw('ROUND(total_amount - subtotal, 2)')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'zone_id')) {
                $table->dropConstrainedForeignId('zone_id');
            }
            foreach (['shipping_area', 'delivery_days', 'currency_code', 'display_currency_code', 'display_exchange_rate'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
