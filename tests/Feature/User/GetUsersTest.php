<?php

use App\Models\User;

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users with user role
 * - [x] admin
 * - [x] second set of data
 */
test('unauthenticated users cannot fetch users', function () {
    $response = $this->get('/api/users');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users with user role cannot fetch users', function () {
    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->get('/api/users');

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('admins can fetch users', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    User::factory(15)->create(); // creating more than User model's `$perPage` value

    $response = $this->actingAs($authedAdmin)->get('/api/users');

    // self in this context refers to the /tests/TestCase class
    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::USER_RESOURCE_KEYS],
            ...self::PAGINATION_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Users'])
        ->assertJsonCount(10, 'data'); // User model's `$perPage` value

    $this->assertAuthenticated();
});

test('can fetch next set of users', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    User::factory(15)->create(); // creating more than User model's `$perPage` value

    $response = $this->actingAs($authedAdmin)->get('/api/users?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
