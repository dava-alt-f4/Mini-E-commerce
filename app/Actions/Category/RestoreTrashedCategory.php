<?php

namespace App\Actions\Category;

use App\Models\Category;

class RestoreTrashedCategory
{
    public function execute(int $categoryId)
    {
        $category = Category::onlyTrashed()->findOrFail($categoryId);

        $category->restore();

        return $category;
    }
}
