<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Webhook\PaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisteredUserController::class);
    Route::post('/login', LoginController::class)->middleware('throttle:login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', LogoutController::class);

    Route::apiResource('cart', CartController::class);

    Route::post('/checkout', CheckoutController::class)->middleware('throttle:checkout');

    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

    // Admin
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('categories', AdminCategoryController::class)->only(['store', 'update', 'destroy'])->names('category');
        Route::apiResource('products', AdminProductController::class)->only(['store', 'update', 'destroy'])->names('product');

        Route::apiResource('orders', AdminOrderController::class)->only('index', 'show', 'update');
    });
});


Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::post('/webhooks/payment', PaymentWebhookController::class)->middleware('throttle:webhook');
