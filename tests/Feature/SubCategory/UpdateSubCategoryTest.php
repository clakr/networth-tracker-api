<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot update sub categories
 * - [x] users cannot update sub categories
 * - [x] cannot update sub categories with empty data
 * - [x] cannot update sub categories with invalid category id
 * - [x] admins can update sub categories
 */

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

test('unauthenticated users cannot update sub categories', function () {
    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->expense())
        ->make();

    $response = $this->putJson("/api/subcategories/{$subCategory->id}", [
        'name' => $subCategoryData->name,
        'category_id' => $subCategoryData->category_id,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertDatabaseHas('sub_categories', [
            'name' => $subCategory->name,
            'category_id' => $subCategory->category_id,
        ])
        ->assertDatabaseMissing('sub_categories', [
            'name' => $subCategoryData->name,
            'category_id' => $subCategoryData->category_id,
        ]);
});

test('users cannot update sub categories', function () {
    $authedUser = User::factory()->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->expense())
        ->make();

    $response = $this->actingAs($authedUser)->putJson("/api/subcategories/{$subCategory->id}", [
        'name' => $subCategoryData->name,
        'category_id' => $subCategoryData->category_id,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertDatabaseHas('sub_categories', [
            'name' => $subCategory->name,
            'category_id' => $subCategory->category_id,
        ])
        ->assertDatabaseMissing('sub_categories', [
            'name' => $subCategoryData->name,
            'category_id' => $subCategoryData->category_id,
        ]);
});

test('cannot update sub categories with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedAdmin)->putJson("/api/subcategories/{$subCategory->id}");

    $response->assertJsonValidationErrors(['name', 'category_id']);

    $this->assertAuthenticated()->assertDatabaseHas('sub_categories', [
        'name' => $subCategory->name,
        'category_id' => $subCategory->category_id,
    ]);
});

test('cannot update sub categories with invalid category id', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->expense())
        ->make();

    $requestBody = [
        'name' => $subCategoryData->name,
        'category_id' => 0,
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/subcategories/{$subCategory->id}", $requestBody);

    $response->assertJsonValidationErrorFor('category_id');

    $this->assertAuthenticated()
        ->assertDatabaseHas('sub_categories', [
            'name' => $subCategory->name,
            'category_id' => $subCategory->category_id,
        ])
        ->assertDatabaseMissing('sub_categories', $requestBody);
});

test('admins can update sub categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->expense())
        ->make();

    $requestBody = [
        'name' => $subCategoryData->name,
        'category_id' => $subCategoryData->category_id,
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/subcategories/{$subCategory->id}", $requestBody);

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => self::SUBCATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Update Subcategory']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('sub_categories', $requestBody)
        ->assertDatabaseMissing('sub_categories', [
            'name' => $subCategory->name,
            'category_id' => $subCategory->category_id,
        ]);
});
