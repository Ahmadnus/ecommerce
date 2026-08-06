<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * This is a virtual model that wraps the customization_config JSON
 * stored on the products table.  It is NOT backed by its own table —
 * it provides a clean API for reading zone definitions and color palettes.
 *
 * Usage:
 *   $config = new ProductCustomization($product->customization_config);
 *   $config->zones();            // array of zone definitions
 *   $config->garmentType();      // e.g. "varsity_jacket"
 *   $config->availableColors();  // ['body' => ['#fff',...], ...]
 *   $config->zoneByKey('A');     // single zone definition or null
 */
class ProductCustomization
{
    private array $data;

    public function __construct(array|null $data)
    {
        $this->data = $data ?? [];
    }

    // ── Zones ──────────────────────────────────────────────────────────────

    /**
     * Returns the array of zone definitions.
     *
     * Each zone:  ['key' => 'A', 'label' => 'Left chest', 'type' => 'both']
     * type: "text" | "image" | "both"
     */
    public function zones(): array
    {
        return $this->data['zones'] ?? [];
    }

    public function zoneByKey(string $key): ?array
    {
        foreach ($this->zones() as $zone) {
            if (($zone['key'] ?? '') === $key) {
                return $zone;
            }
        }

        return null;
    }

    public function zoneKeys(): array
    {
        return array_column($this->zones(), 'key');
    }

    // ── Garment ────────────────────────────────────────────────────────────

    /**
     * Returns the garment template identifier.
     * Maps to a Blade partial: resources/views/customize/garments/{type}.blade.php
     *
     * Supported: "varsity_jacket" | "hoodie" | "graduation_robe"
     */
    public function garmentType(): string
    {
        return $this->data['garment_type'] ?? 'varsity_jacket';
    }

    // ── Colors ─────────────────────────────────────────────────────────────

    /**
     * Returns the available color options per color area.
     * ['body' => ['#141414', '#1d2b53'], 'sleeve' => [...], ...]
     */
    public function availableColors(): array
    {
        return $this->data['available_colors'] ?? [];
    }

    /**
     * Returns default colors (first option per area).
     */
    public function defaultColors(): array
    {
        $defaults = [];
        foreach ($this->availableColors() as $area => $palette) {
            $defaults[$area] = $palette[0] ?? '#ffffff';
        }

        return $defaults;
    }

    // ── Raw access ─────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return $this->data;
    }

    public function isEmpty(): bool
    {
        return empty($this->data);
    }
}
