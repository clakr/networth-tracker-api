<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategory\StoreSubCategoryRequest;
use App\Http\Requests\SubCategory\UpdateSubCategoryRequest;
use App\Http\Resources\SubCategoryResource;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', SubCategory::class);

        $subCategories = SubCategory::with('category')->paginate();

        return SubCategoryResource::collection($subCategories)->additional(['message' => 'SUCCESS: Get Subcategories']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubCategoryRequest $request)
    {
        $subCategory = SubCategory::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
        ]);

        return response([
            'data' => new SubCategoryResource($subCategory),
            'message' => 'SUCCESS: Create Subcategory',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subcategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubCategoryRequest $request, SubCategory $subcategory)
    {

        $subcategory = tap($subcategory)->update([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
        ]);

        return response([
            'data' => new SubCategoryResource($subcategory),
            'message' => 'SUCCESS: Update Subcategory',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subcategory)
    {
        //
    }
}
