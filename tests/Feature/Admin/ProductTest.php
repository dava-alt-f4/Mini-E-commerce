<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

it('can create product', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->postJson('/api/admin/products', [
        'category_id' => $category->id,
        'name' => 'New Product',
        'slug' => 'new-product',
        'description' => 'Product description',
        'price' => 100,
        'stock' => 10,
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Product created successfully.',
        ]);

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'category_id' => $category->id,
    ]);
});

it('can update product', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->putJson("/api/admin/products/{$product->id}", [
        'category_id' => $product->category_id,
        'name' => 'Updated Product',
        'slug' => 'updated-product',
        'description' => 'Updated description',
        'price' => 200,
        'stock' => 20,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product updated successfully.',
        ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
    ]);
});

it('can delete product', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $product = Product::factory()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/admin/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product deleted successfully.',
        ]);

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});
