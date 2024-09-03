<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        return CategoryResource::collection(Category::paginate())->additional(['message' => 'SUCCESS: Get Categories']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
        ]);

        return response([
            'data' => new CategoryResource($category),
            'message' => 'SUCCESS: Create Category',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        Gate::authorize('view', Category::class);

        return response([
            'data' => new CategoryResource($category),
            'message' => 'SUCCESS: Get Category',
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = tap($category)->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
        ]);

        return response([
            'data' => new CategoryResource($category),
            'message' => 'SUCCESS: Update Category',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
