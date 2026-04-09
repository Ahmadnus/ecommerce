<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // تحديد الحقول التي يمكن تعبئتها من خلال الفورم أو updateOrCreate
    protected $fillable = ['key', 'value'];

    public static function get($key, $default = null)
{
    // نبحث في عمود 'key' عن القيمة المطلوبة
    $setting = self::where('key', $key)->first();
    return $setting ? $setting->value : $default;
}
}