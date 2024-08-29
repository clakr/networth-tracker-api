<?php

use App\Models\User;

test('unauthenticated users can not access', function () {
    $response = $this->get('/api/users');

    $this->assertGuest();
    $response->assertUnauthorized();
});

test('users with user role can not access', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/api/users');

    $this->assertAuthenticated();
    $response->assertForbidden();
});

test('admin can access', function () {
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

test('admin can access second set of data', function () {
    $admin = User::factory()->admin()->create();

    User::factory(15)->create(); // creating more than User model's `$perPage` value

    $response = $this->actingAs($admin)->get('/api/users?page=2');

    $response->assertJsonCount(6, 'data'); // User model's `$perPage` value + created admin
});
