<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents the customization choices a customer made for one order line.
 *
 * @property int         $id
 * @property int         $order_id
 * @property int|null    $order_item_id
 * @property int         $product_id
 * @property array|null  $colors          e.g. ['body'=>'#141414','sleeve'=>'#f3f3f1']
 * @property array|null  $texts           e.g. ['A'=>'SMITH','B'=>'23']
 * @property array|null  $selected_zones  e.g. ['A','B','G']
 * @property string|null $notes
 * @property string|null $size            e.g. 'M', 'XL', '2XL'
 * @property string      $status          pending|processing|ready|error
 * @property string|null $rendered_preview_path
 */
class OrderCustomization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'garment_type',        // ← THE FIX: explicitly fillable
        'colors',
        'texts',
        'selected_zones',
        'notes',
        'size',
        'status',
        'rendered_preview_path',
    ];

    protected $casts = [
        'colors'         => 'array',
        'texts'          => 'array',
        'selected_zones' => 'array',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Uploaded images, grouped by zone key via the zone_key column.
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(CustomizationUpload::class)->orderBy('sort_order');
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Returns uploads keyed by zone_key for easy Blade access.
     * ['A' => Collection, 'G' => Collection, ...]
     */
    public function uploadsByZone(): array
    {
        return $this->uploads->groupBy('zone_key')->toArray();
    }

    /**
     * Text string for a specific zone, or empty string.
     *
     * Handles two storage formats:
     *   Legacy (Phase 1):  texts['A'] = 'SMITH'
     *   Rich   (Phase 2):  texts['A'] = ['value'=>'SMITH','color'=>'#fff','fontSize'=>22,...]
     */
    public function textForZone(string $key): string
    {
        $entry = $this->texts[$key] ?? null;

        if ($entry === null) {
            return '';
        }

        // Rich format — extract the value string
        if (is_array($entry)) {
            return (string) ($entry['value'] ?? '');
        }

        // Legacy flat string
        return (string) $entry;
    }

    /**
     * Text style metadata for a zone (Phase 2 rich format only).
     * Returns defaults when stored in legacy flat format.
     */
    public function textStyleForZone(string $key): array
    {
        $entry    = $this->texts[$key] ?? null;
        $defaults = ['color' => '#ffffff', 'fontSize' => 22, 'fontStyle' => 'normal'];

        if (is_array($entry)) {
            return array_merge($defaults, array_intersect_key($entry, $defaults));
        }

        return $defaults;
    }

    /**
     * Color for a specific area, or null.
     */
    public function colorFor(string $area): ?string
    {
        return $this->colors[$area] ?? null;
    }

    /**
     * Whether a zone was selected by the customer.
     */
    public function hasZone(string $key): bool
    {
        return in_array($key, $this->selected_zones ?? [], true);
    }

    /**
     * Human-readable status badge.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'    => 'قيد الانتظار',
            'processing' => 'جارٍ المعالجة',
            'ready'      => 'جاهز',
            'error'      => 'خطأ',
            default      => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'ready'      => 'green',
            'error'      => 'red',
            default      => 'gray',
        };
    }

    public function priceBreakdown(): array
{
    return app(\App\Services\CustomizationPricingService::class)->breakdown($this);
}
 
/**
 * Convenience accessor: $customization->total_price_jod
 * Just the final JOD total, no breakdown.
 */
public function getTotalPriceJodAttribute(): float
{
    return $this->priceBreakdown()['total'];
}
 
}