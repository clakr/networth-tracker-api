<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('users', UserController::class);

    Route::get('categories/all', [CategoryController::class, 'fetchAll']);
    Route::apiResource('categories', CategoryController::class);

    Route::apiResource('subcategories', SubCategoryController::class);
});
