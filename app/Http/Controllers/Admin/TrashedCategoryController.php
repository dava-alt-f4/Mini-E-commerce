<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Category\RestoreTrashedCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class TrashedCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->onlyTrashed()->paginate(10);

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $category = Category::with('products')->onlyTrashed()->findOrFail($id);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id, RestoreTrashedCategory $restoreTrashed)
    {
        $category = $restoreTrashed->execute($id);

        return response()->json([
            'message' => 'Category restored successfully.',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
