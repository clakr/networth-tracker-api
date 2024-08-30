<?php

use App\Models\User;

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users with user role
 * - [x] admin
 * - [x] second set of data
 */
describe('cannot get users', function () {
    test('without authentication', function () {
        $response = $this->get('/api/users');

        $this->assertGuest();

        $response->assertUnauthorized();
    });

    test('with user role', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/users');

        $this->assertAuthenticated();

        $response->assertForbidden();
    });
});

describe('can get users', function () {
    test('with admin role', function () {
        $admin = User::factory()->admin()->create();

        User::factory(15)->create(); // creating more than User model's `$perPage` value

        $response = $this->actingAs($admin)->get('/api/users');

        // self in this context refers to the /tests/TestCase class
        $this->assertAuthenticated();

        $response->assertOk()
            ->assertExactJsonStructure([
                'message',
                'data' => ['*' => self::USER_RESOURCE_KEYS],
                ...self::PAGINATION_KEYS,
            ])
            ->assertJsonFragment(['message' => 'SUCCESS: Get Users'])
            ->assertJsonCount(10, 'data'); // User model's `$perPage` value
    });
});

test('can get next set of users', function () {
    $admin = User::factory()->admin()->create();

    User::factory(15)->create(); // creating more than User model's `$perPage` value

    $response = $this->actingAs($admin)->get('/api/users?page=2');

    $response->assertJsonCount(6, 'data'); // User model's `$perPage` value + created admin
});
