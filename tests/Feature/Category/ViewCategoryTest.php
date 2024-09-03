<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot fetch categories
 * - [x] users cannot fetch categories
 * - [x] admins can fetch categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot fetch categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $response = $this->getJson("/api/categories/{$category->id}");

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot fetch categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->getJson("/api/categories/{$category->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can fetch categories', function () {
    $category = Category::factory()
        ->income()
        ->create();

    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->getJson("/api/categories/{$category->id}");

    $response->assertOk()
        ->assertExactJsonStructure([
            'data' => self::CATEGORY_RESOURCE_KEYS,
            'message',
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Category']);

    $this->assertAuthenticated();
});
