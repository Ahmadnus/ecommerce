<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تنظيف الكاش (عشان ما يعلق السيستم على بيانات قديمة)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. إنشاء الصلاحيات
        $manageCatalog = Permission::firstOrCreate(['name' => 'manage-catalog']);
        $manageAll     = Permission::firstOrCreate(['name' => 'manage-all']);

        // 3. إنشاء رتبة "Admin" واحدة للكل
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 4. إنشاء مستخدم (السوبر أدمن)
        $superAdmin = User::updateOrCreate(
            ['email' => 'super@admin.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '0123456789',
                'password' => Hash::make('password'),
            ]
        );
        // بنعطيه رتبة أدمن + كل الصلاحيات
        $superAdmin->assignRole($adminRole);
        $superAdmin->givePermissionTo([$manageCatalog, $manageAll]);

        // 5. إنشاء مستخدم (الأدمن العادي)
        $regularAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Regular Admin',
                'phone'    => '0987654321',
                'password' => Hash::make('password'),
            ]
        );
        // بنعطيه رتبة أدمن + صلاحية الكتالوج فقط
        $regularAdmin->assignRole($adminRole);
        $regularAdmin->givePermissionTo($manageCatalog);

        $this->command->info('Two Admins created with different permissions!');
    }
}