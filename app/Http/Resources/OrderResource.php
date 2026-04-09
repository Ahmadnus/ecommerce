<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'order_number'   => $this->order_number,
            'status'         => $this->status,
            'payment_status' => $this->payment_status,
            'subtotal'       => (float) $this->subtotal,
            'tax'            => (float) $this->tax_amount,
            'shipping'       => (float) $this->shipping_amount,
            'total'          => (float) $this->total_amount,
            'shipping_to'    => [
                'name'    => $this->shipping_name,
                'email'   => $this->shipping_email,
                'address' => $this->shipping_address,
                'city'    => $this->shipping_city,
                'zip'     => $this->shipping_zip,
                'country' => $this->shipping_country,
            ],
            'items'      => $this->items->map(fn($item) => [
                'product_id'   => $item->product_id,
                'product_name' => $item->product_name,
                'quantity'     => $item->quantity,
                'unit_price'   => (float) $item->unit_price,
                'total_price'  => (float) $item->total_price,
            ]),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
