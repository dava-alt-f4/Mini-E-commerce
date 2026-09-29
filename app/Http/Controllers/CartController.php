<?php

namespace App\Http\Controllers;

use App\Actions\AddProductToCart;
use App\Http\Requests\AddToCartRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cart = Cart::with('cartItems.product')
            ->firstOrCreate(['user_id' => $request->user()->id]);

        return new CartResource($cart);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddToCartRequest $request, AddProductToCart $addProductToCart)
    {
        $user = $request->user();
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $result = $addProductToCart->execute($user, $productId, $quantity);

        return response()->json($result);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
