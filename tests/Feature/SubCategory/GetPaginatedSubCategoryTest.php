<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot fetch sub categories
 * - [x] users cannot fetch sub categories
 * - [x] admins can fetch sub categories
 * - [x] can get next set of sub categories
 */

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

test('unauthenticated users cannot fetch sub categories', function () {
    $response = $this->getJson('/api/subcategories');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot fetch categories', function () {
    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->getJson('/api/subcategories');

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can fetch categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    SubCategory::factory(15)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedAdmin)->getJson('/api/subcategories');

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::SUBCATEGORY_RESOURCE_KEYS],
            ...self::PAGINATION_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Subcategories'])
        ->assertJsonCount(10, 'data');

    $this->assertAuthenticated();
});

test('can fetch next set of sub categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    SubCategory::factory(15)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedAdmin)->getJson('/api/subcategories?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
