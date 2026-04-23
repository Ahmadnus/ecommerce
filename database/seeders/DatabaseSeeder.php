<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // استدعاء ملف المشرفين فقط وتجاهل باقي الداتا
        $this->call([
            AdminSeeder::class,
        ]);
    }
}