<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;

class UpdateCartItem
{
    public function execute(User $user, int $cartItemId, int $quantity)
    {
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = $cart->cartItems()->findOrFail($cartItemId);

        $cartItem->quantity = $quantity;
        $cartItem->save();

        return $cartItem;
    }
}
