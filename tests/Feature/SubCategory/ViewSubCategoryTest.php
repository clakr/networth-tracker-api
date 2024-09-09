<?php

/**
 * CASES:
 * - [ ] unauthenticated users cannot view sub categories
 * - [ ] users cannot view sub categories
 * - [ ] admins can view sub categories
 */

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

test('unauthenticated users cannot view sub categories', function () {
    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $response = $this->getJson("/api/subcategories/{$subCategory->id}");

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot view sub categories', function () {
    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->getJson("/api/subcategories/{$subCategory->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can view sub categories', function () {
    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->getJson("/api/subcategories/{$subCategory->id}");

    $response->assertOk()
        ->assertExactJsonStructure([
            'data' => [
                ...self::SUBCATEGORY_RESOURCE_KEYS,
                'category' => self::CATEGORY_RESOURCE_KEYS,
            ],
            'message',
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Subcategory']);

    $this->assertAuthenticated();
});
