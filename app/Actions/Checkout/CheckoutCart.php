<?php

namespace App\Actions\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutCart
{
    public function execute(User $user)
    {
        $cart = Cart::with('cartItems.product')->where('user_id', $user->id)->firstOrFail();

        if ($cart->cartItems->isEmpty()) {
            return ['message' => 'Cart is empty.'];
        }

        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        return DB::transaction(function () use ($user, $cart) {
            $totalPrice = $this->calculateTotalAmount($cart);
            $orderNumber = $this->createOrderNumber();

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            $itemDetails = [];
            $orderItemsData = [];

            foreach ($cart->cartItems as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);

                if (!$product || $product->stock < $item->quantity) {
                    abort(422, "Insufficient stock for product '{$item->product->name}'.");
                }

                $product->decrement('stock', $item->quantity);

                $itemDetails[] = [
                    'id' => (string) $item->product_id,
                    'price' => (int) $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => substr($item->product->name, 0, 50),
                ];

                $orderItemsData[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ];
            }

            $order->orderItems()->createMany($orderItemsData);

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $totalPrice,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ];

            $snapUrl = Snap::getSnapUrl($params);

            $order->update(['snap_url' => $snapUrl]);

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
