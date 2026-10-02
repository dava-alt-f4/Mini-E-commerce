<?php

use App\Models\User;

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('Password123!'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'user' => ['id', 'name', 'email'],
            'token',
        ]);
});

it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'password' => bcrypt('Password123!'),
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'WrongPassword123!',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});
