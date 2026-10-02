<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

it('can get user cart', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create();
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->getJson('/api/cart');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'items' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'quantity',
                        'subtotal',
                    ],
                ],
                'total',
            ],
        ]);
});

it('can add product to cart', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock' => 10]);

    $response = $this->actingAs($user)->postJson('/api/cart', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product added to cart successfully.',
        ]);

    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('can update cart item quantity', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create(['stock' => 10]);
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->putJson("/api/cart/{$cartItem->id}", [
        'quantity' => 5,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Cart item updated successfully.',
        ]);

    $this->assertDatabaseHas('cart_items', [
        'id' => $cartItem->id,
        'quantity' => 5,
    ]);
});

it('can remove item from cart', function () {
    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create();
    $cartItem = CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/cart/{$cartItem->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Cart item removed successfully.',
        ]);

    $this->assertDatabaseMissing('cart_items', [
        'id' => $cartItem->id,
    ]);
});
