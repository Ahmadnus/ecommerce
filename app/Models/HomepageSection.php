<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomepageSection extends Model
{
    use HasFactory;

    // ── Predefined homepage render slots ────────────────────────────────────
    public const POSITION_TOP_HERO          = 'top_hero';
    public const POSITION_BELOW_CATEGORIES  = 'below_categories';
    public const POSITION_ABOVE_FOOTER      = 'above_footer';

    public const POSITIONS = [
        self::POSITION_TOP_HERO         => 'القسم العلوي (Top Hero)',
        self::POSITION_BELOW_CATEGORIES => 'قسم تحت التصنيفات (Below Categories)',
        self::POSITION_ABOVE_FOOTER     => 'قسم اسفل الشاشة فوق الفوتر (Above Footer)',
    ];

    // ── Section type (modular page-builder block) ───────────────────────────
    // Every structural element of the homepage is a sortable section type, so
    // the admin can position ANYTHING, ANYWHERE via sort_order alone.
    public const TYPE_HERO_BANNER     = 'hero_banner';
    public const TYPE_CATEGORIES_GRID = 'categories_grid';
    public const TYPE_PRODUCT_GRID    = 'product_grid';
    public const TYPE_CUSTOM_IMAGE    = 'custom_image';
    public const TYPE_TEXT_BLOCK      = 'text_block';

    // Legacy alias — pre-unification media/text sections were stored as
    // 'banner'; still rendered (as a hero banner) for backward compatibility.
    public const TYPE_BANNER = 'banner';

    public const SECTION_TYPES = [
        self::TYPE_HERO_BANNER     => 'بانر رئيسي — النص فوق الصورة (Hero Overlay)',
        self::TYPE_BANNER          => 'بانر مقسم — الصورة ثم النص أسفلها (Stacked Banner)',
        self::TYPE_CATEGORIES_GRID => 'شبكة التصنيفات (Categories Grid)',
        self::TYPE_PRODUCT_GRID    => 'شبكة منتجات (Product Grid)',
        self::TYPE_CUSTOM_IMAGE    => 'صورة ترويجية مستقلة (Custom Image)',
        self::TYPE_TEXT_BLOCK      => 'كتلة نصية (عنوان ووصف)',
    ];

    /**
     * Section types that render an uploaded image/video (media fields shown
     * in the admin, media rendered on the storefront).
     */
    public function usesMedia(): bool
    {
        return in_array($this->section_type, [
            self::TYPE_HERO_BANNER,
            self::TYPE_CUSTOM_IMAGE,
            self::TYPE_BANNER,
        ], true);
    }

    // ── Product source (only when section_type = product_grid) ──────────────
    public const SOURCE_LATEST       = 'latest_products';
    public const SOURCE_BEST_SELLERS = 'best_sellers';
    public const SOURCE_FEATURED     = 'featured';

    // Non-category, "smart" sources. Any other product_source value is treated
    // as a category id (see resolveProducts()).
    public const PRODUCT_SOURCES = [
        self::SOURCE_LATEST       => 'أحدث المنتجات (Latest)',
        self::SOURCE_BEST_SELLERS => 'الأكثر مبيعاً (Best Sellers)',
        self::SOURCE_FEATURED     => 'منتجات مميزة (Featured)',
    ];

    // ── Text & button position relative to media ────────────────────────────
    public const POS_OVERLAY_CENTER = 'overlay_center';
    public const POS_OVERLAY_LEFT   = 'overlay_left';
    public const POS_OVERLAY_RIGHT  = 'overlay_right';
    public const POS_BELOW_IMAGE    = 'below_image';

    public const TEXT_POSITIONS_MAP = [
        self::POS_OVERLAY_CENTER => 'فوق الصورة — وسط (Overlay Center)',
        self::POS_OVERLAY_LEFT   => 'فوق الصورة — يسار (Overlay Left)',
        self::POS_OVERLAY_RIGHT  => 'فوق الصورة — يمين (Overlay Right)',
        self::POS_BELOW_IMAGE    => 'أسفل الصورة (Below Image)',
    ];

    // ── Media frame aspect ratio ────────────────────────────────────────────
    public const RATIO_LANDSCAPE = 'landscape';
    public const RATIO_PORTRAIT  = 'portrait';
    public const RATIO_SQUARE    = 'square';
    public const RATIO_FULL      = 'full';

    public const ASPECT_RATIOS = [
        self::RATIO_LANDSCAPE => 'عرضي — بانر عريض (Landscape)',
        self::RATIO_PORTRAIT  => 'طولي — صورة فاخرة (Portrait)',
        self::RATIO_SQUARE    => 'مربع 1:1 (Square)',
        self::RATIO_FULL      => 'كامل الشاشة (Full Screen)',
    ];

    // Tailwind classes per ratio. 'full' is handled separately (h-screen
    // full-bleed) by the renderer, so it maps to null here.
    private const ASPECT_RATIO_CLASSES = [
        self::RATIO_LANDSCAPE => 'aspect-[21/9] md:aspect-[3/1]',
        self::RATIO_PORTRAIT  => 'aspect-[3/4] md:aspect-[2/3]',
        self::RATIO_SQUARE    => 'aspect-square',
    ];

    // ── Text alignment choices ──────────────────────────────────────────────
    public const ALIGN_LEFT   = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT  = 'right';

    public const TEXT_ALIGNMENTS = [
        self::ALIGN_LEFT   => 'يسار (Left)',
        self::ALIGN_CENTER => 'وسط (Center)',
        self::ALIGN_RIGHT  => 'يمين (Right)',
    ];

    // ── Font family choices (التحكم في الخط) ────────────────────────────────
    public const FONT_DEFAULT = '';
    public const FONT_DIDONE  = 'didone';

    public const FONT_FAMILIES = [
        self::FONT_DEFAULT => 'افتراضي (Default)',
        self::FONT_DIDONE  => 'Didone / Modern Serif',
    ];

    // CSS font-stack for each non-default choice, with a safe serif fallback.
    private const FONT_FAMILY_CSS = [
        self::FONT_DIDONE => "'Bodoni Moda', 'Playfair Display', serif",
    ];

    protected $fillable = [
        'title',
        'paragraph',
        'media_path',
        'media_type',
        'position',
        'section_type',
        'product_source',
        'text_position',
        'aspect_ratio',
        'button_text',
        'button_url',
        'section_title_accent_color',
        'text_color',
        'button_bg_color',
        'button_text_color',
        'text_alignment',
        'title_font_family',
        'paragraph_font_family',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        // Secondary id ordering keeps rows with equal sort_order stable
        // instead of DB-engine-dependent (previously several rows shared 0).
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Legacy rows may have a NULL/empty section_type (created before the
     * modular builder). Default them to the stacked 'banner' renderer so
     * they always display instead of falling through to nothing.
     */
    public function getSectionTypeAttribute($value): string
    {
        return ($value !== null && $value !== '') ? $value : self::TYPE_BANNER;
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    // ── Layout helpers (media block engine) ─────────────────────────────────

    /**
     * Whether the text/CTA is drawn ON TOP of the media (any overlay_*),
     * as opposed to stacked cleanly below it.
     */
    public function isOverlayText(): bool
    {
        return in_array($this->text_position, [
            self::POS_OVERLAY_CENTER, self::POS_OVERLAY_LEFT, self::POS_OVERLAY_RIGHT,
        ], true);
    }

    /**
     * Horizontal alignment classes for the overlay content container.
     * (Vertical centering is always applied by the renderer.)
     */
    public function overlayAlignmentClasses(): string
    {
        return match ($this->text_position) {
            self::POS_OVERLAY_LEFT  => 'items-start text-left',
            self::POS_OVERLAY_RIGHT => 'items-end text-right',
            default                 => 'items-center text-center',
        };
    }

    public function isFullScreenMedia(): bool
    {
        return ($this->aspect_ratio ?? self::RATIO_FULL) === self::RATIO_FULL;
    }

    /**
     * Tailwind aspect-ratio utility classes for the media frame, or null for
     * the full-screen variant (rendered edge-to-edge at h-screen instead).
     */
    public function aspectRatioClasses(): ?string
    {
        return self::ASPECT_RATIO_CLASSES[$this->aspect_ratio] ?? null;
    }

    // ── Section-type helpers ────────────────────────────────────────────────
    public function isHeroBanner(): bool
    {
        return in_array($this->section_type, [self::TYPE_HERO_BANNER, self::TYPE_BANNER], true);
    }

    public function isCategoriesGrid(): bool
    {
        return $this->section_type === self::TYPE_CATEGORIES_GRID;
    }

    public function isCustomImage(): bool
    {
        return $this->section_type === self::TYPE_CUSTOM_IMAGE;
    }

    public function isBanner(): bool
    {
        return $this->section_type === self::TYPE_BANNER;
    }

    public function isTextBlock(): bool
    {
        return $this->section_type === self::TYPE_TEXT_BLOCK;
    }

    public function isProductGrid(): bool
    {
        return $this->section_type === self::TYPE_PRODUCT_GRID;
    }

    /**
     * Human label for the chosen product source — either one of the "smart"
     * sources or the matched category name.
     */
    public function productSourceLabel(): ?string
    {
        if (! $this->isProductGrid() || ! $this->product_source) {
            return null;
        }

        if (isset(self::PRODUCT_SOURCES[$this->product_source])) {
            return self::PRODUCT_SOURCES[$this->product_source];
        }

        if (ctype_digit((string) $this->product_source)) {
            return optional(Category::find($this->product_source))->name
                ?? 'تصنيف #' . $this->product_source;
        }

        return $this->product_source;
    }

    /**
     * Resolve the products this grid should display, based on product_source.
     * Only relevant when section_type = product_grid. Returns an empty
     * collection for any other type.
     */
    public function resolveProducts(int $limit = 12)
    {
        if (! $this->isProductGrid()) {
            return Product::query()->whereRaw('1 = 0')->get();
        }

        $query = Product::active()->with([
            'categories',
            'variants' => fn ($q) => $q->where('is_active', true),
        ]);

        switch ($this->product_source) {
            case self::SOURCE_LATEST:
                $query->latest();
                break;

            case self::SOURCE_BEST_SELLERS:
                // Popularity proxy: number of order-item lines referencing the
                // product. Falls back to newest when there are no orders yet.
                $query->withCount('orderItems')
                      ->orderByDesc('order_items_count')
                      ->latest();
                break;

            case self::SOURCE_FEATURED:
                $query->where('is_featured', true)->orderBy('sort_order');
                break;

            default:
                // Any numeric value is treated as a category id.
                if (ctype_digit((string) $this->product_source)) {
                    $categoryId = (int) $this->product_source;
                    $query->whereHas(
                        'categories',
                        fn ($q) => $q->where('categories.id', $categoryId)
                    )->orderBy('sort_order');
                } else {
                    $query->latest();
                }
        }

        return $query->limit($limit)->get();
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (! $this->media_path) {
            return null;
        }

        // Use the same disk as the rest of the app's media (configured via
        // MEDIA_DISK / config/media-library.php) rather than the built-in
        // "public" disk — on this server they point at different physical
        // directories, and only the media-library disk is web-served.
        return Storage::disk(config('media-library.disk_name', 'public'))->url($this->media_path);
    }

    public function hasMedia(): bool
    {
        return $this->media_type !== 'none' && ! empty($this->media_path);
    }

    public function hasButton(): bool
    {
        return ! empty($this->button_text) && ! empty($this->button_url);
    }

    /**
     * CSS font-family stack for the admin-chosen title font, or null to
     * fall back to the site's default heading font.
     */
    public function titleFontFamilyCss(): ?string
    {
        return self::FONT_FAMILY_CSS[$this->title_font_family] ?? null;
    }

    /**
     * CSS font-family stack for the admin-chosen paragraph font, or null to
     * fall back to the site's default body font.
     */
    public function paragraphFontFamilyCss(): ?string
    {
        return self::FONT_FAMILY_CSS[$this->paragraph_font_family] ?? null;
    }

    /**
     * Whether this section's chosen fonts require a Google Font not already
     * loaded site-wide (e.g. Bodoni Moda for the Didone/Modern Serif choice).
     */
    public function needsGoogleFont(): bool
    {
        return $this->title_font_family === self::FONT_DIDONE
            || $this->paragraph_font_family === self::FONT_DIDONE;
    }
}
