<?php

namespace App\Actions;

use App\Models\Cart;
use App\Models\User;

class RemoveCartItem
{
    public function execute(User $user, int $cartItemId)
    {
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = $cart->cartItems()->findOrFail($cartItemId);

        $cartItem->delete();

        return ['message' => 'Cart item removed successfully.'];
    }
}

