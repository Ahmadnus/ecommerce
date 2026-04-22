<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Creates the `otpsettings` table (renamed from `settings`).
     * Seeds the 7 SMS/OTP configuration rows with null values so
     * the system falls back to config/sms.php defaults until the
     * admin fills them in via the dashboard.
     *
     * If you already have a `settings` table you want to RENAME
     * instead of recreating, replace the Schema::create block with:
     *   Schema::rename('settings', 'otpsettings');
     */
    public function up(): void
    {
        // Skip if table was already created (e.g. from a previous migration)
        if (Schema::hasTable('otpsettings')) {
            return;
        }

        Schema::create('otpsettings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('group', 60)->default('sms');
            $table->string('label', 200)->nullable();
            $table->boolean('is_secret')->default(false);
            $table->timestamps();
        });

        $now = now();

        DB::table('otpsettings')->insert([
            [
                'key'        => 'sms_url',
                'value'      => null,
                'group'      => 'sms',
                'label'      => 'رابط الـ API للرسائل النصية',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'sms_user',
                'value'      => null,
                'group'      => 'sms',
                'label'      => 'اسم المستخدم (SMS API)',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'sms_pass',
                'value'      => null,
                'group'      => 'sms',
                'label'      => 'كلمة المرور (SMS API)',
                'is_secret'  => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'sms_sid',
                'value'      => null,
                'group'      => 'sms',
                'label'      => 'Sender ID (اسم المُرسِل)',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'sms_type',
                'value'      => null,
                'group'      => 'sms',
                'label'      => 'نوع الرسالة (4 = Unicode عربي)',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'otp_ttl_minutes',
                'value'      => '5',
                'group'      => 'sms',
                'label'      => 'مدة صلاحية رمز OTP (بالدقائق)',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key'        => 'otp_length',
                'value'      => '6',
                'group'      => 'sms',
                'label'      => 'طول رمز OTP (عدد الأرقام)',
                'is_secret'  => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('otpsettings');
    }
};