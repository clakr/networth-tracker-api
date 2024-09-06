<?php

/**
 * CASES
 * - [x] unauthenticated users cannot delete categories
 * - [x] users cannot delete categories
 * - [x] admins can delete categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot delete categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $response = $this->deleteJson("/api/categories/{$category->id}");

    $response->assertUnauthorized();

    $this->assertGuest()->assertModelExists($category);
});

test('users cannot delete categories', function () {
    $authedUser = User::factory()->make();

    $category = Category::factory()
        ->income()
        ->create();

    $response = $this->actingAs($authedUser)->deleteJson("/api/categories/{$category->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelExists($category);
});

test('admins can delete categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $category = Category::factory()
        ->income()
        ->create();

    $response = $this->actingAs($authedAdmin)->deleteJson("/api/categories/{$category->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete Category']);

    $this->assertAuthenticated()->assertModelMissing($category);
});
