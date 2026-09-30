<?php

namespace App\Actions\Order;

use App\Models\Order;

class UpdateOrderStatus
{
    public function execute(array $input, int $orderId)
    {
        $order = Order::findOrFail($orderId);

        $order->update([
            'status' => $input['status'],
        ]);

        return $order;
    }
}
