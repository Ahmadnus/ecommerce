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
        // 1. تنظيف الكاش الخاص بصلاحيات Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. إنشاء الصلاحيات
        $manageCatalog = Permission::firstOrCreate(['name' => 'manage-catalog']);
        $manageAll     = Permission::firstOrCreate(['name' => 'manage-all']);

        // 3. إنشاء رتبة "Admin"
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // 4. إنشاء مستخدم (السوبر أدمن) برقم يبدأ بـ 0962
        $superAdmin = User::updateOrCreate(
            ['phone' => '+962790000000'], // البحث صار بالرقم لأنه المعرف الأساسي عندك
            [
                'name'     => 'Super Admin',
                'email'    => 'super@admin.com',
                'password' => Hash::make('password'), // يفضل تغييره لاحقاً
            ]
        );
        
        $superAdmin->assignRole($adminRole);
        $superAdmin->givePermissionTo([$manageCatalog, $manageAll]);

        // 5. إنشاء مستخدم (الأدمن العادي) برقم يبدأ بـ 0962
        $regularAdmin = User::updateOrCreate(
            ['phone' => '+962790000001'],
            [
                'name'     => 'Regular Admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('password'),
            ]
        );

        $regularAdmin->assignRole($adminRole);
        $regularAdmin->givePermissionTo($manageCatalog);

        $this->command->info('Admins created successfully with 0962 phone prefix!');
    }
}