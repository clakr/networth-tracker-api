<?php

/**
 * CASES:
 * - [ ] unauthenticated user cannot get categories
 * - [ ] users cannot get categories
 * - [ ] admins can get categories
 */

use App\Enums\CategoryType;
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

    Category::factory()->create([
        'name' => 'Test Name',
        'type' => CategoryType::INCOME->value,
    ]);

    $response = $this->actingAs($admin)->get('/api/categories');

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::CATEGORY_RESOURCE_KEYS],
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Categories'])
        ->assertJsonCount(1, 'data');

    $this->assertAuthenticated();
});
