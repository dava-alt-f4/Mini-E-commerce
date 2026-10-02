<?php

use App\Models\Order;
use App\Models\User;

it('can list user orders', function () {
    $user = User::factory()->create();
    Order::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'order_number',
                    'total_price',
                    'status',
                    'snap_url',
                    'order_date',
                ],
            ],
        ]);
});

it('can show user order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/orders/{$order->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'username',
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
            ],
        ]);
});

it('cannot show other user order', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($user)->getJson("/api/orders/{$order->id}");

    $response->assertStatus(404);
});
