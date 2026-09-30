<?php

namespace App\Actions\Product;

use App\Models\Product;

class UpdateProduct
{
    public function execute(array $input, int $productId)
    {
        $product = Product::findOrFail($productId);

        $product->update($input);

        return $product;
    }
}
