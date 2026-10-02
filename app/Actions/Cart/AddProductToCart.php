<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;

class AddProductToCart
{
    public function execute(User $user, int $productId, int $quantity)
    {
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = $cart->cartItems()->firstOrNew(['product_id' => $productId]);
        $product = Product::findOrFail($productId);

        if (($cartItem->quantity += $quantity) > $product->stock) {
            abort(422, "Only {$cartItem->product->stock} left in stock");
        }

        $cartItem->save();

        return [
            'message' => 'Product added to cart successfully.',
        ];
    }
}
