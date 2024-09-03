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

    $this->assertGuest();
});

test('users cannot delete categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->deleteJson("/api/categories/{$category->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can delete categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->deleteJson("/api/categories/{$category->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete Category']);

    $this->assertAuthenticated();
});
