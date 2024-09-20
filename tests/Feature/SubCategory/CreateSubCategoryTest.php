<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot create sub categories
 * - [x] users cannot create sub categories
 * - [x] cannot create sub categories with empty data
 * - [x] cannot create sub categories with invalid category id
 * - [x] admins can create sub categories
 */

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

test('unauthenticated users cannot create sub categories', function () {
    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->income())
        ->make();

    $response = $this->postJson('/api/subcategories', [
        'category_id' => $subCategoryData->category_id,
        'name' => $subCategoryData->name,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot create sub categories', function () {
    $authedUser = User::factory()->make();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->income())
        ->make();

    $response = $this->actingAs($authedUser)->postJson('/api/subcategories', [
        'category_id' => $subCategoryData->category_id,
        'name' => $subCategoryData->name,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('cannot create sub categories with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/subcategories');

    $response->assertJsonValidationErrors(['name', 'category_id']);

    $this->assertAuthenticated();
});

test('cannot create sub categories with invalid category id', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategoryData = SubCategory::factory()->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/subcategories', [
        'category_id' => 0,
        'name' => $subCategoryData->name,
    ]);

    $response->assertJsonValidationErrorFor('category_id');

    $this->assertAuthenticated();
});

test('admins can create sub categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategoryData = SubCategory::factory()
        ->for(Category::factory()->income())
        ->make();

    $requestBody = [
        'category_id' => $subCategoryData->category_id,
        'name' => $subCategoryData->name,
    ];

    $response = $this->actingAs($authedAdmin)->postJson('/api/subcategories', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::SUBCATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create Subcategory']);

    $this->assertAuthenticated()->assertDatabaseHas('sub_categories', $requestBody);
});
