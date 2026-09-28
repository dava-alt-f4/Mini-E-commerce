<?php

namespace App\Http\Controllers;

use App\Actions\CreateNewUser;
use App\Http\Requests\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request, CreateNewUser $createNewUser)
    {
        $user = $createNewUser->create($request->validated());

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
