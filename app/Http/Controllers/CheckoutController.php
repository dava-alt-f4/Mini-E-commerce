<?php

namespace App\Http\Controllers;

use App\Actions\Checkout\CheckoutCart;
use App\Http\Resources\Order\OrderResource;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, CheckoutCart $checkoutCart)
    {
        $result = $checkoutCart->execute($request->user());

        if (isset($result['message'])) {
            return response()->json($result, 400);
        }

        return response()->json(new OrderResource($result));
    }
}
