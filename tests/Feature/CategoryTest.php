<?php

use App\Models\Category;

it('can list categories', function () {
    Category::factory()->count(3)->create();

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                ],
            ],
        ]);
});

it('can show category', function () {
    $category = Category::factory()->create();

    $response = $this->getJson("/api/categories/{$category->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'items',
            ],
        ]);
});
