<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource
 *
 * Transforms a Product model into a consistent API response shape.
 * This decouples your API response format from your DB schema.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'name'                => $this->name,
            'slug'                => $this->slug,
            'short_description'   => $this->short_description,
            'description'         => $this->description,
            'price'               => (float) $this->price,
            'sale_price'          => $this->sale_price ? (float) $this->sale_price : null,
            'effective_price'     => (float) $this->effective_price,
            'is_on_sale'          => $this->is_on_sale,
            'discount_percentage' => $this->discount_percentage,
            'stock_quantity'      => $this->stock_quantity,
            'in_stock'            => $this->in_stock,
            'sku'                 => $this->sku,
            'image'               => $this->image,
            'images'              => $this->images ?? [],
            'is_featured'         => $this->is_featured,
            'category'            => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
