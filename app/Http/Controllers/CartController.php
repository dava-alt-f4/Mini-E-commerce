<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddProductToCart;
use App\Actions\Cart\RemoveCartItem;
use App\Actions\Cart\UpdateCartItem;
use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Resources\Cart\CartResource;
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
     * Update quantity of the item in the cart.
     */
    public function update(Request $request, int $id, UpdateCartItem $updateCartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $result = $updateCartItem->execute($request->user(), $id, $request->input('quantity'));

        return response()->json([
            'message' => 'Cart item updated successfully.',
            'data' => $result,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id, RemoveCartItem $removeCartItem)
    {
        $result = $removeCartItem->execute($request->user(), $id);

        return response()->json($result);
    }
}
