<?php

use App\Models\User;

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users with user role
 * - [x] empty data
 * - [x] invalid email
 * - [x] not unique email
 * - [x] invalid role
 * - [x] without role and password values
 * - [x] admin
 */
test('unauthenticated users cannot create user', function () {
    $userData = User::factory()->make();

    $response = $this->postJson('/api/users', [
        'name' => $userData->name,
        'email' => $userData->email,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()->assertModelMissing($userData);
});

test('users with user role cannot create user', function () {
    $authedUser = User::factory()->make();

    $userData = User::factory()->make();

    $response = $this->actingAs($authedUser)->postJson('/api/users', [
        'name' => $userData->name,
        'email' => $userData->email,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelMissing($userData);
});

test('cannot create user with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/users');

    $response->assertJsonValidationErrors(['name', 'email']);

    $this->assertAuthenticated();
});

test('cannot create user with invalid email', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $userData = User::factory()->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/users', [
        'name' => $userData->name,
        'email' => 'THIS IS INVALID EMAIL',
    ]);

    $response->assertJsonValidationErrorFor('email');

    $this->assertAuthenticated()->assertModelMissing($userData);
});
test('cannot create user with not unique email', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->create(); // this test needs an existing `User` instance in the database that is why we need to use `create()` instead of `make()`

    $userData = User::factory()->make();

    $response = $this->actingAs($authedAdmin)
        ->postJson('/api/users', [
            'name' => $userData->name,
            'email' => $authedAdmin->email,
        ]);

    $response->assertJsonValidationErrorFor('email');

    $this->assertAuthenticated()->assertModelMissing($userData);
});

test('cannot create user with invalid role', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $userData = User::factory()->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/users', [
        'name' => $userData->name,
        'email' => $userData->email,
        'role' => 'SUPERADMIN',
    ]);

    $response->assertJsonValidationErrorFor('role');

    $this->assertAuthenticated()->assertModelMissing($userData);
});

test('admins can create user', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $userData = User::factory()->make();

    $requestBody = [
        'name' => $userData->name,
        'email' => $userData->email,
        'role' => $userData->role,
    ];

    $response = $this->actingAs($authedAdmin)->postJson('/api/users', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::USER_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create User']);

    $this->assertAuthenticated()->assertDatabaseHas('users', $requestBody);
});

test('can create user without role and password specified', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $userData = User::factory()->make();

    $requestBody = [
        'name' => $userData->name,
        'email' => $userData->email,
    ];

    $this->actingAs($authedAdmin)->postJson('/api/users', $requestBody);

    $this->assertAuthenticated()->assertDatabaseHas('users', $requestBody);
});
