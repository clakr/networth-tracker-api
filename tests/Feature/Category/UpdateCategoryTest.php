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
    $existingCategory = Category::factory()
        ->income()
        ->create();
    $category = Category::factory()
        ->expense()
        ->make();

    $response = $this->putJson("/api/categories/{$existingCategory->id}", [
        'name' => $category->name,
        'type' => $category->type,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertDatabaseHas('categories', [
            'name' => $existingCategory->name,
            'type' => $existingCategory->type,
        ])
        ->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ]);
});

test('users with user role cannot update a category', function () {
    $existingCategory = Category::factory()
        ->income()
        ->create();
    $category = Category::factory()
        ->expense()
        ->make();

    $response = $this->actingAs(User::factory()->create())
        ->putJson("/api/categories/{$existingCategory->id}", [
            'name' => $category->name,
            'type' => $category->type,
        ]);

    $response->assertForbidden();

    $this->assertAuthenticated()->assertDatabaseHas('categories', [
        'name' => $existingCategory->name,
        'type' => $existingCategory->type,
    ])->assertDatabaseMissing('categories', [
        'name' => $category->name,
        'type' => $category->type,
    ]);
});

test('cannot update a category with empty data', function () {
    $admin = User::factory()->admin()->create();

    $existingCategory = Category::factory()
        ->income()
        ->create();

    $response = $this->actingAs($admin)->putJson("/api/categories/{$existingCategory->id}");

    $response->assertJsonValidationErrors(['name', 'type']);
});

test('cannot update category with invalid type', function () {
    $admin = User::factory()->admin()->create();

    $existingCategory = Category::factory()
        ->income()
        ->create();
    $category = Category::factory()
        ->expense()
        ->make();

    $response = $this->actingAs($admin)->putJson("/api/categories/{$existingCategory->id}", [
        'name' => $category->name,
        'type' => 'INVALID TYPE',
    ]);

    $response->assertJsonValidationErrorFor('type');

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', [
            'name' => $existingCategory->name,
            'type' => $existingCategory->type,
        ])
        ->assertDatabaseMissing('categories', [
            'name' => $category->name,
            'type' => $category->type,
        ]);
});

test('admins can update a category', function () {
    $admin = User::factory()->admin()->create();

    $existingCategory = Category::factory()
        ->income()
        ->create();
    $category = Category::factory()
        ->expense()
        ->make();

    $requestBody = [
        'name' => $category->name,
        'type' => $category->type,
    ];

    $response = $this->actingAs($admin)->putJson("/api/categories/{$existingCategory->id}", $requestBody);

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => self::CATEGORY_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Update Category']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('categories', $requestBody)
        ->assertDatabaseMissing('categories', [
            'name' => $existingCategory->name,
            'type' => $existingCategory->type,
        ]);
});
