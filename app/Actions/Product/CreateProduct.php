<?php

namespace App\Actions\Product;

use App\Models\Product;

class CreateProduct
{
    public function execute(array $input)
    {
        return Product::create($input);
    }
}
