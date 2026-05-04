<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'langsetting',
                'value' => 'both',
            ],
            [
                'key' => 'available_lang_ar',
                'value' => '1',
            ],
            [
                'key' => 'available_lang_en',
                'value' => '1',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}