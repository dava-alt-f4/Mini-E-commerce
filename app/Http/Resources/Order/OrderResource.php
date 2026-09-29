<?php

namespace App\Http\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->whenLoaded('user', fn () => $this->user->name),
            'order_number' => $this->order_number,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'order_date' => $this->created_at->format('Y-m-d H:i:s'),
            'items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
        ];
    }
}
