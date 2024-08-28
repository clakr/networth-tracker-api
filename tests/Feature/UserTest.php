<?php

use App\Models\User;

describe('GET: /api/users', function () {
    test('response status is `HTTP_UNAUTHORIZED` if user is not authenticated', function () {
        $response = $this->get('/api/users');

        $response->assertUnauthorized();
    });

    test("response status is `HTTP_FORBIDDEN` if user's role is user", function () {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/api/users');

        $response->assertForbidden();
    });

    test("response status is `HTTP_OK` if user's role is admin", function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        $response->assertOk();
    });

    test('the JSON structure', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        // self in this context refers to the /tests/TestCase class
        $response->assertExactJsonStructure([
            ...self::PAGINATION_KEYS,
            'data' => ['*' => self::USER_RESOURCE_KEYS],
            'message',
        ]);
    });

    test('the value of the `message` JSON key', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        $response->assertJsonFragment(['message' => 'SUCCESS: Get Users']);
    });

    test('the specified number of paginated users', function () {
        User::factory(15)->create(); // creating more than User model's `$perPage` value

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        $response->assertJsonCount(10, 'data'); // User model's `$perPage` value
    });

    test('the second page of paginated users', function () {
        User::factory(15)->create(); // creating more than User model's `$perPage` value

        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users?page=2');

        $response->assertJsonCount(6, 'data'); // User model's `$perPage` value + created admin
    });

});
