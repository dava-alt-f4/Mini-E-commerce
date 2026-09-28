<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\User;

class AddProductToCart
{
    public function execute(User $user, int $productId, int $quantity)
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = $cart->cartItems()->firstOrNew(['product_id' => $productId]);
        $cartItem->quantity += $quantity;
        $cartItem->save();

        return [
            'message' => 'Product added to cart successfully.',
        ];
    }
}
