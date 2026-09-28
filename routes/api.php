<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisteredUserController::class);
    Route::post('/login', LoginController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class);
});
