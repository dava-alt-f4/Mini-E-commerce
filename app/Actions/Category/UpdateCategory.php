<?php

namespace App\Actions\Category;

use App\Models\Category;

class UpdateCategory
{
    public function execute(array $input, int $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $category->update($input);

        return $category;
    }
}
