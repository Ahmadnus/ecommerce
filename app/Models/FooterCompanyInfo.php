<?php
// app/Models/FooterCompanyInfo.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class FooterCompanyInfo extends Model implements HasMedia
{
    use HasTranslations, InteractsWithMedia;

    protected $table = 'footer_company_info';

    protected $fillable = [
        'company_name',
        'description',
        'location',
        'phone',
        'phone_country_code',
        'is_active',
        'sort_order',
    ];

    public array $translatable = [
        'company_name',
        'description',
        'location',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('flag_icon')
            ->singleFile();
    }

    /**
     * Formatted tel: href — always LTR-safe
     */
    public function getTelHrefAttribute(): string
    {
        return 'tel:' . preg_replace('/\s+/', '', $this->phone ?? '');
    }

    /**
     * Convenience scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}