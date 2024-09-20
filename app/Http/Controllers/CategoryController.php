<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::allowIf(fn (User $user) => $user->isAdmin());

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
        Gate::allowIf(fn (User $user) => $user->isAdmin());

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
        Gate::allowIf(fn (User $user) => $user->isAdmin());

        $category->delete();

        return response([
            'message' => 'SUCCESS: Delete Category',
        ], Response::HTTP_OK);
    }

    public function fetchAll()
    {
        Gate::allowIf(fn (User $user) => $user->isAdmin());

        return CategoryResource::collection(Category::all())
            ->additional(['message' => 'SUCCESS: Get All Categories']);
    }
}
