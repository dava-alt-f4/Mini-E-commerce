<?php

use App\Models\Category;
use App\Models\User;

it('can create category', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->postJson('/api/admin/categories', [
        'name' => 'New Category',
        'slug' => 'new-category',
        'description' => 'Category description',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Category added successfully.',
        ]);

    $this->assertDatabaseHas('categories', [
        'name' => 'New Category',
    ]);
});

it('can update category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->putJson("/api/admin/categories/{$category->id}", [
        'name' => 'Updated Category',
        'slug' => 'updated-category',
        'description' => 'Updated description',
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Category updated successfully.',
        ]);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Updated Category',
    ]);
});

it('can delete category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/admin/categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Category deleted successfully',
        ]);

    $this->assertSoftDeleted('categories', [
        'id' => $category->id,
    ]);
});

it('cannot access admin routes as customer', function () {
    $customer = User::factory()->create(['role' => 'customer']);

    $response = $this->actingAs($customer)->postJson('/api/admin/categories', [
        'name' => 'New Category',
    ]);

    $response->assertStatus(403);
});
