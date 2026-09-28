<?php

namespace App\Http\Controllers;

use App\Actions\AuthenticateUser;
use App\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request, AuthenticateUser $authenticateUser)
    {
        $user = $authenticateUser->authenticate($request->validated());

        if (! $user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
