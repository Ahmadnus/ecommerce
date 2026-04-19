<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
   protected $fillable = ['content', 'sort_order', 'is_active'];
}
