<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCustomization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'colors',
        'texts',
        'selected_zones',
        'notes',
        'status',
        'rendered_preview_path',
    ];

    protected $casts = [
        'colors'         => 'array',
        'texts'          => 'array',
        'selected_zones' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function uploads(): HasMany
    {
        return $this->hasMany(CustomizationUpload::class)->orderBy('sort_order');
    }

    public function uploadsByZone(): array
    {
        return $this->uploads->groupBy('zone_key')->toArray();
    }

    public function textForZone(string $key): string
    {
        $entry = $this->texts[$key] ?? null;

        if ($entry === null) {
            return '';
        }

        if (is_array($entry)) {
            return (string) ($entry['value'] ?? '');
        }

        return (string) $entry;
    }

    public function textStyleForZone(string $key): array
    {
        $entry    = $this->texts[$key] ?? null;
        $defaults = ['color' => '#ffffff', 'fontSize' => 22, 'fontStyle' => 'normal'];

        if (is_array($entry)) {
            return array_merge($defaults, array_intersect_key($entry, $defaults));
        }

        return $defaults;
    }

    public function colorFor(string $area): ?string
    {
        return $this->colors[$area] ?? null;
    }

    public function hasZone(string $key): bool
    {
        return in_array($key, $this->selected_zones ?? [], true);
    }

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
}