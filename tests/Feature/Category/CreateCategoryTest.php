<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot create categories
 * - [x] users with user role cannot create categories
 * - [x] cannot create category with empty data
 * - [x] cannot create category with invalid type
 * - [x] admins can create categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot create categories', function () {
    $categoryData = Category::factory()
        ->income()
        ->make();

    $response = $this->postJson('/api/categories', [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()->assertModelMissing($categoryData);
});

test('users with user role cannot create categories', function () {
    $authedUser = User::factory()->make();

    $categoryData = Category::factory()
        ->income()
        ->make();

    $response = $this->actingAs($authedUser)->postJson('/api/categories', [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelMissing($categoryData);
});

test('cannot create category with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/categories');

    $response->assertJsonValidationErrors(['name', 'type']);

    $this->assertAuthenticated();
});

test('cannot create category with invalid type', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $categoryData = Category::factory()
        ->income()
        ->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/categories', [
        'name' => $categoryData->name,
        'type' => 'INVALID TYPE',
    ]);

    $response->assertJsonValidationErrorFor('type');

    $this->assertAuthenticated()->assertModelMissing($categoryData);
});

test('admins can create categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $categoryData = Category::factory()
        ->income()
        ->make();

    $requestBody = [
        'name' => $categoryData->name,
        'type' => $categoryData->type,
    ];

    $response = $this->actingAs($authedAdmin)->postJson('/api/categories', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::CATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create Category']);

    $this->assertAuthenticated()->assertDatabaseHas('categories', $requestBody);
});
