<?php

use App\Models\Order;
use App\Models\User;

it('can list all orders', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    Order::factory()->count(3)->create();

    $response = $this->actingAs($admin)->getJson('/api/admin/orders');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'username',
                    'order_number',
                    'total_price',
                    'status',
                    'snap_url',
                    'order_date',
                ],
            ],
        ]);
});

it('can show order', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/admin/orders/{$order->id}");

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

it('can update order status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = Order::factory()->create(['status' => 'pending']);

    $response = $this->actingAs($admin)->putJson("/api/admin/orders/{$order->id}", [
        'status' => 'shipped',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Order status updated successfully.',
        ]);

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'shipped',
    ]);
});
