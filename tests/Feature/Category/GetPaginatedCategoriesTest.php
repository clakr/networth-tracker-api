<?php

/**
 * CASES:
 * - [x] unauthenticated user cannot fetch categories
 * - [x] users cannot fetch categories
 * - [x] admins can fetch categories
 * - [x] can fetch next set of categories
 * - [x] can fetch all categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot fetch categories', function () {
    $response = $this->getJson('/api/categories');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot fetch categories', function () {
    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->getJson('/api/categories');

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can fetch categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    Category::factory(15)
        ->income()
        ->create();

    $response = $this->actingAs($authedAdmin)->getJson('/api/categories');

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::CATEGORY_RESOURCE_KEYS],
            ...self::PAGINATION_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Categories'])
        ->assertJsonCount(10, 'data');

    $this->assertAuthenticated();
});

test('can fetch next set of categories', function () {
    $authedAdmin = User::factory()->admin()->make();

    Category::factory(15)
        ->income()
        ->create();

    $response = $this->actingAs($authedAdmin)->getJson('/api/categories?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});

test('can fetch all categories', function () {
    $authedAdmin = User::factory()->admin()->make();

    Category::factory(15)
        ->income()
        ->create();

    $response = $this->actingAs($authedAdmin)->getJson('/api/categories/all');

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::CATEGORY_RESOURCE_KEYS],
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get All Categories'])
        ->assertJsonCount(15, 'data');

    $this->assertAuthenticated();
});
