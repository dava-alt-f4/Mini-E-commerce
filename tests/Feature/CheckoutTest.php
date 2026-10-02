<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;

it('can checkout cart', function () {
    Mockery::mock('alias:Midtrans\Snap')
        ->shouldReceive('getSnapUrl')
        ->andReturn('https://app.sandbox.midtrans.com/snap/v2/vtweb/1234567890');

    $user = User::factory()->create();
    $cart = Cart::factory()->create(['user_id' => $user->id]);
    $product = Product::factory()->create(['stock' => 10, 'price' => 100]);
    CartItem::factory()->create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($user)->postJson('/api/checkout');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'order_number',
            'total_price',
            'status',
            'snap_url',
            'order_date',
            'items' => [
                '*' => [
                    'id',
                    'product_name',
                    'quantity',
                    'price',
                    'subtotal',
                ],
            ],
        ]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'total_price' => 200,
    ]);

    $this->assertDatabaseMissing('cart_items', [
        'cart_id' => $cart->id,
    ]);
});

it('cannot checkout empty cart', function () {
    $user = User::factory()->create();
    Cart::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->postJson('/api/checkout');

    $response->assertStatus(400)
        ->assertJson([
            'message' => 'Cart is empty.',
        ]);
});
