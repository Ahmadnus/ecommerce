<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_reference')->unique();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();

            $table->foreignId('pickup_location_id')
                  ->nullable()->constrained('rental_locations')->nullOnDelete();
            $table->foreignId('return_location_id')
                  ->nullable()->constrained('rental_locations')->nullOnDelete();

            // ── Schedule ────────────────────────────────────────────────────
            $table->dateTime('pickup_date_time');
            $table->dateTime('return_date_time');
            $table->unsignedInteger('total_days')->default(1);
            $table->string('rate_plan')->default('daily'); // daily | weekly | monthly

            // ── Driver / customer details ───────────────────────────────────
            $table->string('driver_name');
            $table->string('driver_email')->nullable();
            $table->string('driver_phone');
            $table->date('driver_date_of_birth')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->string('driver_license_country')->nullable();
            $table->date('driver_license_expiry')->nullable();
            $table->string('driver_national_id')->nullable();

            // ── Money ───────────────────────────────────────────────────────
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('location_fee', 10, 2)->default(0);   // pickup + one-way
            $table->decimal('extras_total', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);      // VAT
            $table->decimal('security_deposit', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 8)->default('SAR');

            // Snapshot of chosen add-ons, e.g. [{"key":"cdw","label":"...","price":30}]
            $table->json('extras')->nullable();

            // ── Status ──────────────────────────────────────────────────────
            // pending | confirmed | active | completed | cancelled
            $table->string('booking_status')->default('pending');
            $table->string('payment_method')->default('on_pickup');
            $table->string('payment_status')->default('pending');

            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['booking_status', 'pickup_date_time']);
            $table->index('booking_reference');
            $table->index(['vehicle_id', 'pickup_date_time', 'return_date_time']);
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('bookings');
        Schema::enableForeignKeyConstraints();
    }
};
