<?php

use App\Models\User;

it('can logout an authenticated user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'User logged out successfully',
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('cannot logout an unauthenticated user', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});
