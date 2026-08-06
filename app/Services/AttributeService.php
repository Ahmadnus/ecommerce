<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\AttributeValue;

/**
 * AttributeService — business logic for the admin attributes and
 * attribute-values CRUD. Never returns views/redirects.
 */
class AttributeService
{
    // ── Attributes ─────────────────────────────────────────────────────────────

    public function getAttributesWithValueCounts()
    {
        return Attribute::withCount('values')
            ->orderBy('sort_order')
            ->get();
    }

    public function getAttributesOrdered()
    {
        return Attribute::orderBy('sort_order')->get();
    }

    public function createAttribute(array $data): Attribute
    {
        return Attribute::create([
            'name'       => ['ar' => $data['name']['ar'], 'en' => $data['name']['en']],
            'type'       => $data['type'] ?? 'select',
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function updateAttribute(Attribute $attribute, array $data): Attribute
    {
        $attribute->update([
            'name'       => ['ar' => $data['name']['ar'], 'en' => $data['name']['en']],
            'type'       => $data['type'] ?? $attribute->type,
            'sort_order' => $data['sort_order'] ?? $attribute->sort_order,
        ]);

        return $attribute;
    }

    public function deleteAttribute(Attribute $attribute): void
    {
        $attribute->delete();
    }

    // ── Attribute values ───────────────────────────────────────────────────────

    public function getValuesWithAttributes()
    {
        return AttributeValue::with('attribute')->latest()->get();
    }

    public function createValue(array $data): AttributeValue
    {
        return AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
            'value'        => ['ar' => $data['value']['ar'],  'en' => $data['value']['en']],
            'label'        => ['ar' => $data['label']['ar'] ?? '', 'en' => $data['label']['en'] ?? ''],
            'color_hex'    => $data['color_hex'] ?? null,
        ]);
    }

    public function updateValue(AttributeValue $attributeValue, array $data): AttributeValue
    {
        $attributeValue->update([
            'attribute_id' => $data['attribute_id'],
            'value'        => ['ar' => $data['value']['ar'],  'en' => $data['value']['en']],
            'label'        => ['ar' => $data['label']['ar'] ?? '', 'en' => $data['label']['en'] ?? ''],
            'color_hex'    => $data['color_hex'] ?? null,
        ]);

        return $attributeValue;
    }

    public function deleteValue(AttributeValue $attributeValue): void
    {
        $attributeValue->delete();
    }
}
