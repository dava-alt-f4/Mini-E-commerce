<?php

namespace App\Actions\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CheckoutCart
{
    public function execute(User $user)
    {
        $cart = Cart::with('cartItems.product')->where('user_id', $user->id)->firstOrFail();

        if ($cart->cartItems->isEmpty()) {
            return ['message' => 'Cart is empty.'];
        }

        return DB::transaction(function () use ($user, $cart) {
            $order = Order::create([
                'order_number' => $this->createOrderNumber(),
                'user_id' => $user->id,
                'total_price' => $this->calculateTotalAmount($cart),
                'status' => 'pending',
            ]);

            $orderItemsData = $cart->cartItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price, // Historical price
                ];
            });

            $order->orderItems()->createMany($orderItemsData->toArray());
            $cart->cartItems()->delete();

            return $order->load(['user', 'orderItems.product']);
        });
    }

    private function calculateTotalAmount(Cart $cart)
    {
        return $cart->cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    private function createOrderNumber()
    {
        $date = now()->format('Ymd');
        $lastOrder = Order::whereDate('created_at', now()->toDateString())->latest('id')->first();

        $lastOrderNumber = $lastOrder ? (int) substr($lastOrder->order_number, -4) : 0;
        $newOrderNumber = str_pad($lastOrderNumber + 1, 4, '0', STR_PAD_LEFT);

        return "INV-{$date}-{$newOrderNumber}";
    }
}
