<?php

namespace App\Actions\Product;

use App\Models\Product;

class DeleteProduct
{
    public function execute(int $productId)
    {
        $product = Product::findOrFail($productId);

        $product->delete();

        return ['message' => 'Product deleted successfully.'];
    }
}
