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
    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/categories/{$category->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can delete categories', function () {
    $category = Category::factory()
        ->income()
        ->create();
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/categories/{$category->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete Category']);

    $this->assertAuthenticated();
});
