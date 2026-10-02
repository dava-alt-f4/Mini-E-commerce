<?php

namespace App\Actions\Product;

use App\Models\Product;

class RestoreTrashedProduct
{
    public function execute(int $productId)
    {
        $product = Product::onlyTrashed()->findOrFail($productId);

        $product->restore();

        return $product;
    }
}
