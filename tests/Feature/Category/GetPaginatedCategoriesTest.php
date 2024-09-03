<?php

/**
 * CASES:
 * - [x] unauthenticated user cannot get categories
 * - [x] users cannot get categories
 * - [x] admins can get categories
 */

use App\Models\Category;
use App\Models\User;

test('unauthenticated users cannot fetch categories', function () {
    $response = $this->get('/api/categories');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot fetch categories', function () {
    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->get('/api/categories');

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

    $response = $this->actingAs($authedAdmin)->get('/api/categories');

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

    $response = $this->actingAs($authedAdmin)->get('/api/categories?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
