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
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/api/categories');

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can fetch categories', function () {
    $admin = User::factory()->admin()->create();

    Category::factory(15)
        ->income()
        ->create();

    $response = $this->actingAs($admin)->get('/api/categories');

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
    $admin = User::factory()->admin()->create();

    Category::factory(15)
        ->income()
        ->create();

    $response = $this->actingAs($admin)->get('/api/categories?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
