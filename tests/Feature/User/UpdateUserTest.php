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
 * - [x] admin
 */
test('unauthenticated users cannot update user', function () {
    $user = User::factory()->create();

    $userData = User::factory()->make();

    $response = $this->putJson("/api/users/{$user->id}", [
        'name' => $userData->name,
        'email' => $userData->email,
        'role' => $userData->role,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ])
        ->assertDatabaseMissing('users', [
            'name' => $userData->name,
            'email' => $userData->email,
            'role' => $userData->role,
        ]);
});

test('users with user role cannot update user', function () {
    $authedUser = User::factory()->make();

    $user = User::factory()->create();

    $userData = User::factory()->make();

    $response = $this->actingAs($authedUser)
        ->putJson("/api/users/{$user->id}", [
            'name' => $userData->name,
            'email' => $userData->email,
            'role' => $userData->role,
        ]);

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ])
        ->assertDatabaseMissing('users', [
            'name' => $userData->name,
            'email' => $userData->email,
            'role' => $userData->role,
        ]);
});

test('cannot update user with empty data', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $user = User::factory()->create();

    $response = $this->actingAs($authedAdmin)->putJson("/api/users/{$user->id}");

    $response->assertJsonValidationErrors(['name', 'email', 'role']);

    $this->assertAuthenticated()->assertDatabaseHas('users', [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ]);
});

test('cannot update user with invalid email', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $user = User::factory()->create();

    $userData = User::factory()->make();

    $requestbody = [
        'name' => $userData->name,
        'email' => 'this is invalid email',
        'role' => $userData->role,
    ];

    $response = $this->actingas($authedAdmin)->putjson("/api/users/{$user->id}", $requestbody);

    $response->assertjsonvalidationerrorfor('email');

    $this->assertauthenticated()
        ->assertdatabasehas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ])
        ->assertdatabasemissing('users', $requestbody);
});

test('cannot update user with not unique email', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->create();

    $user = User::factory()->create();

    $userData = User::factory()->make();

    $requestBody = [
        'name' => $userData->name,
        'email' => $authedAdmin->email,
        'role' => $userData->role,
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/users/{$user->id}", $requestBody);

    $response->assertJsonValidationErrorFor('email');

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ])
        ->assertDatabaseMissing('users', $requestBody);
});

test('cannot update user with invalid role value', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $user = User::factory()->create();

    $userData = User::factory()->make();

    $requestBody = [
        'name' => $userData->name,
        'email' => $userData->email,
        'role' => 'SUPERADMIN',
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/users/{$user->id}", $requestBody);

    $response->assertJsonValidationErrorFor('role');

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ])
        ->assertDatabaseMissing('users', $requestBody);
});

test('admins can update user', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $user = User::factory()->create();

    $userData = User::factory()->make();

    $requestBody = [
        'name' => $userData->name,
        'email' => $userData->email,
        'role' => $userData->role,
    ];

    $response = $this->actingAs($authedAdmin)->putJson("/api/users/{$user->id}", $requestBody);

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => self::USER_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Update User']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', $requestBody)
        ->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
});
