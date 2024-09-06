<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot update a category
 * - [x] users with user role cannot update a category
 * - [x] cannot update a category with empty data
 * - [x] cannot update category with invalid type
 * - [x] admins can update a category
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot update a category', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $categoryData = Category::factory()
        ->expense()
        ->make();

    $response = $this->putJson("/api/categories/{$category->id}", [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertDatabaseHas('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ])
        ->assertDatabaseMissing('categories', [
            'name' => $categoryData->name,
            'type' => $categoryData->type,
        ]);
});

test('users with user role cannot update a category', function () {
    $authedUser = User::factory()->make();

    $category = Category::factory()
        ->income()
        ->create();

    $categoryData = Category::factory()
        ->expense()
        ->make();

    $response = $this->actingAs($authedUser)->putJson("/api/categories/{$category->id}", [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ])
        ->assertDatabaseMissing('categories', [
            'name' => $categoryData->name,
            'type' => $categoryData->type,
        ]);
});

test('cannot update a category with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $category = Category::factory()
        ->income()
        ->create();

    $response = $this->actingAs($authedAdmin)->putJson("/api/categories/{$category->id}");

    $response->assertJsonValidationErrors(['name', 'type']);

    $this->assertAuthenticated()->assertDatabaseHas('categories', [
        'name' => $category->name,
        'type' => $category->type,
    ]);
});

test('cannot update category with invalid type', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $category = Category::factory()
        ->income()
        ->create();

    $categoryData = Category::factory()
        ->expense()
        ->make();

    $requestBody = [
        'name' => $categoryData->name,
        'type' => 'INVALID TYPE',
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/categories/{$category->id}", $requestBody);

    $response->assertJsonValidationErrorFor('type');

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ])
        ->assertDatabaseMissing('categories', $requestBody);
});

test('admins can update a category', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $category = Category::factory()
        ->income()
        ->create();

    $categoryData = Category::factory()
        ->expense()
        ->make();

    $requestBody = [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/categories/{$category->id}", $requestBody);

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => self::CATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Update Category']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', $requestBody)
        ->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ]);
});
