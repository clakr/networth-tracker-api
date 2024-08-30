<?php

use App\Models\User;

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users with user role
 * - [x] empty data
 * - [x] invalid email
 * - [x] invalid role
 * - [x] admin
 */
test('unauthenticated users cannot update resources', function () {
    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $response = $this->putJson("/api/users/{$existingUser->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
    ]);

    $this->assertGuest();

    $response->assertUnauthorized();
});

test('users with user role cannot update all resources', function () {
    $existingUser = User::factory()->create();
    $user = User::factory()->make();

    $response = $this->actingAs(User::factory()->create())
        ->putJson("/api/users/{$existingUser->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);

    $this->assertAuthenticated();

    $response->assertForbidden();
});

test('can not update user with empty data', function () {
    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->create();

    $response = $this->actingAs($admin)
        ->putJson("/api/users/{$existingUser->id}");

    $response->assertJsonValidationErrors(['name', 'email', 'role']);
});

test('can not update user with not unique email', function () {
    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->create();

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}", [
        'name' => $existingUser->name,
        'email' => $admin->email,
        'role' => $existingUser->role,
    ]);

    $response->assertJsonValidationErrorFor('email');
});

test('can not update user with invalid role value', function () {
    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->create();

    $response = $this->actingAs($admin)->putJson("/api/users/{$existingUser->id}", [
        'name' => $existingUser->name,
        'email' => $existingUser->email,
        'role' => 'invalid',
    ]);

    $response->assertJsonValidationErrorFor('role');
});

test('admin can update all resources', function () {
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

    $this->assertDatabaseMissing('users', [
        'name' => $existingUser->name,
        'email' => $existingUser->email,
        'role' => $existingUser->role,
    ])->assertDatabaseHas('users', $requestBody);
});
