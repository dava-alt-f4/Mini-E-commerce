<?php

namespace App\Actions;

use App\Models\User;

class CreateNewUser
{
    public function create(array $input): User
    {
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => $input['role'] ?? 'customer',
        ]);
    }
}
