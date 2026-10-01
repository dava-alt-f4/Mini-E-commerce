<?php

namespace App\Actions\Category;

use App\Models\Category;

class CreateCategory
{
    public function execute(array $input)
    {
        return Category::create($input);
    }
}
