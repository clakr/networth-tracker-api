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
    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $response = $this->putJson("/api/users/{$existingUser->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertDatabaseHas('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ])
        ->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
});

test('users with user role cannot update user', function () {
    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $response = $this->actingAs(User::factory()->create())
        ->putJson("/api/users/{$existingUser->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ])
        ->assertDatabaseMissing('users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
});

test('cannot update user with empty data', function () {
    $admin = User::factory()->admin()->create();

    $existingUser = User::factory()->create();

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}");

    $response->assertJsonValidationErrors(['name', 'email', 'role']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ]);
});

test('cannot update user with invalid email', function () {
    $admin = user::factory()->admin()->create();

    $existinguser = user::factory()->create();
    $user = user::factory()->make();

    $requestbody = [
        'name' => $user->name,
        'email' => 'this is invalid email',
        'role' => $user->role,
    ];

    $response = $this->actingas($admin)->putjson("/api/users/{$existinguser->id}", $requestbody);

    $response->assertjsonvalidationerrorfor('email');

    $this->assertauthenticated()
        ->assertdatabasehas('users', [
            'name' => $existinguser->name,
            'email' => $existinguser->email,
            'role' => $existinguser->role,
        ])
        ->assertdatabasemissing('users', $requestbody);
});

test('cannot update user with not unique email', function () {
    $admin = user::factory()->admin()->create();

    $existingUser = user::factory()->create();
    $user = user::factory()->make();

    $requestBody = [
        'name' => $user->name,
        'email' => $admin->email,
        'role' => $user->role,
    ];

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}", $requestBody);

    $response->assertJsonValidationErrorFor('email');

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ])
        ->assertDatabaseMissing('users', $requestBody);
});

test('cannot update user with invalid role value', function () {
    $admin = User::factory()->admin()->create();

    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $requestBody = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'SUPERADMIN',
    ];

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}", $requestBody);

    $response->assertJsonValidationErrorFor('role');

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ])
        ->assertDatabaseMissing('users', $requestBody);
});

test('admins can update user', function () {
    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $requestBody = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ];

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}", $requestBody);

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => self::USER_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Update User']);

    $this->assertAuthenticated()
        ->assertDatabaseHas('users', $requestBody)
        ->assertDatabaseMissing('users', [
            'name' => $existingUser->name,
            'email' => $existingUser->email,
            'role' => $existingUser->role,
        ]);
});
