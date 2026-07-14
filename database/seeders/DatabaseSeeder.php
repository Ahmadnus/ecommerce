<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
               JordanCountrySeeder::class,
            TestSeeder::class,
        ]);

        // ─────────────────────────────────────────────
        // Categories (Arabic + English)
        // ─────────────────────────────────────────────
       
}}