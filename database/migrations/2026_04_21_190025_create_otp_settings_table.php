<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * otpsettings table
     * ─────────────────────────────────────────────────────────────────────────
     * Stores dynamic key/value pairs. Used primarily for SMS API credentials
     * and any other per-tenant white-label configuration.
     *
     * Priority: DB value (otpsettings) → hardcoded default (see config/sms.php)
     */
    public function up(): void
    {
        // تم تغيير اسم الجدول إلى otpsettings
        Schema::create('otpsettings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 60)->default('sms')
                  ->comment('Groups settings in the admin UI (sms, general, etc.)');
            $table->string('label', 200)->nullable()
                  ->comment('Human-readable Arabic label shown in the admin form');
            $table->boolean('is_secret')->default(false)
                  ->comment('Masks value in the UI like a password field');
            $table->timestamps();
        });

        // ── Seed default SMS settings (values intentionally empty — ──────────
        // ── the system falls back to config/sms.php hardcoded values) ────────
        $now = now();
        DB::table('otpsettings')->insert([
            [
                'key'       => 'sms_url',
                'value'     => null,
                'group'     => 'sms',
                'label'     => 'رابط الـ API للرسائل النصية',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'sms_user',
                'value'     => null,
                'group'     => 'sms',
                'label'     => 'اسم المستخدم (SMS API)',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'sms_pass',
                'value'     => null,
                'group'     => 'sms',
                'label'     => 'كلمة المرور (SMS API)',
                'is_secret' => true,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'sms_sid',
                'value'     => null,
                'group'     => 'sms',
                'label'     => 'Sender ID (اسم المُرسِل)',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'sms_type',
                'value'     => null,
                'group'     => 'sms',
                'label'     => 'نوع الرسالة (4 = Unicode عربي)',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'otp_ttl_minutes',
                'value'     => '5',
                'group'     => 'sms',
                'label'     => 'مدة صلاحية رمز OTP (بالدقائق)',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
            [
                'key'       => 'otp_length',
                'value'     => '6',
                'group'     => 'sms',
                'label'     => 'طول رمز OTP (عدد الأرقام)',
                'is_secret' => false,
                'created_at'=> $now,
                'updated_at'=> $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('otpsettings');
    }
};