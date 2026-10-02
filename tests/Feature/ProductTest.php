<?php

use App\Models\Product;

it('can list products', function () {
    Product::factory()->count(3)->create();

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'price',
                    'stock',
                ],
            ],
            'links',
            'meta',
        ]);
});

it('can search products', function () {
    Product::factory()->create(['name' => 'Apple iPhone']);
    Product::factory()->create(['name' => 'Samsung Galaxy']);

    $response = $this->getJson('/api/products?search=Apple');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Apple iPhone');
});

it('can show product', function () {
    $product = Product::factory()->create();

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'price',
                'stock',
                'category',
            ],
        ]);
});
