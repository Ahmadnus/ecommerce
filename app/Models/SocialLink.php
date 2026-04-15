<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
protected $fillable = [
    'platform_name', 
    'url', 
    'icon_svg', 
    'sort_order', 
    'is_active', 
    'whatsapp_number', // إضافة هذا
    'is_floating'      // إضافة هذا
];
}
