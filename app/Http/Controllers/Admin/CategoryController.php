<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Category\CreateCategory;
use App\Actions\Category\DeleteCategory;
use App\Actions\Category\UpdateCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request, CreateCategory $createCategory)
    {
        $category = $createCategory->execute($request->validated());

        return response()->json([
            'message' => 'Category added successfully.',
            'data' => $category,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, int $id, UpdateCategory $updateCategory)
    {
        $category = $updateCategory->execute($request->validated(), $id);

        return response()->json([
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, DeleteCategory $deleteCategory)
    {
        $category = $deleteCategory->execute($id);

        return response()->json($category);
    }
}
