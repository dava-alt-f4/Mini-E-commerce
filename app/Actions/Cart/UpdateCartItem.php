<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\User;

class UpdateCartItem
{
    public function execute(User $user, int $cartItemId, int $quantity)
    {
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = $cart->cartItems()->findOrFail($cartItemId);

        if ($quantity > $cartItem->product->stock)
            {
                abort(422, "Only {$cartItem->product->stock} left in stock");
            }

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }
}
