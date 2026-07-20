<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Represents a single uploaded image file for one zone in an order customization.
 *
 * @property int         $id
 * @property int         $order_customization_id
 * @property string      $zone_key
 * @property string      $path               relative to storage/app/public/
 * @property string|null $original_filename
 * @property string|null $mime_type
 * @property int|null    $size_bytes
 * @property int|null    $width_px
 * @property int|null    $height_px
 * @property int         $sort_order
 */
class CustomizationUpload extends Model
{
    protected $fillable = [
        'order_customization_id',
        'zone_key',
        'path',
        'original_filename',
        'mime_type',
        'size_bytes',
        'width_px',
        'height_px',
        'sort_order',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function orderCustomization(): BelongsTo
    {
        return $this->belongsTo(OrderCustomization::class);
    }

    // ── URL helpers ─────────────────────────────────────────────────────────

    /**
     * Public URL for use in <img> tags.
     */
    public function url(): string
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Absolute filesystem path — used by Phase 2 compositing.
     */
    public function absolutePath(): string
    {
        return Storage::disk('public')->path($this->path);
    }

    /**
     * Human-readable file size.
     */
    public function formattedSize(): string
    {
        if (! $this->size_bytes) {
            return '–';
        }

        return round($this->size_bytes / 1024, 1) . ' KB';
    }
}
