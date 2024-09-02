<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot create categories
 * - [x] users with user role cannot create categories
 * - [x] cannot create category with empty data
 * - [x] cannot create category with invalid type
 * - [ ] admins can create categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot create categories', function () {
    $category = Category::factory()
        ->income()
        ->make();

    $response = $this->postJson('/api/categories', [
        'name' => $category->name,
        'type' => $category->type,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertModelMissing($category);
});

test('users with user role cannot create categories', function () {
    $user = User::factory()->create();
    $category = Category::factory()
        ->income()
        ->make();

    $response = $this->actingAs($user)
        ->postJson('/api/categories', [
            'name' => $category->name,
            'type' => $category->type,
        ]);

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertModelMissing($category);
});

test('cannot create category with empty data', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/categories');

    $response->assertJsonValidationErrors(['name', 'type']);

    $this->assertAuthenticated();
});

test('cannot create category with invalid type', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()
        ->income()
        ->make();

    $response = $this->actingAs($admin)
        ->postJson('/api/categories', [
            'name' => $category->name,
            'type' => 'INVALID TYPE',
        ]);

    $response->assertJsonValidationErrorFor('type');

    $this->assertAuthenticated()
        ->assertModelMissing($category);
});

test('admins can create categories', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()
        ->income()
        ->make();

    $requestBody = [
        'name' => $category->name,
        'type' => $category->type,
    ];

    $response = $this->actingAs($admin)->postJson('/api/categories', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::CATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create Category']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', $requestBody);
});
