<?php

namespace App\Actions\Category;

use App\Models\Category;

class DeleteCategory
{
    public function execute(int $categoryId)
    {
        $category = Category::findOrFail($categoryId);

        $category->delete();

        return ['message' => 'Category deleted successfully'];
    }
}
