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
describe('cannot update user', function () {
    test('without authentication', function () {
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

    test('with user role', function () {
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

    test('with empty data', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}");

        $response->assertJsonValidationErrors(['name', 'email', 'role']);
    });

    test('with invalid email', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
            'name' => $user->name,
            'email' => 'THIS IS INVALID EMAIL',
            'role' => $user->role,
        ]);

        $response->assertJsonValidationErrorFor('email');
    });

    test('with not unique email', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
            'name' => $user->name,
            'email' => $admin->email,
            'role' => $user->role,
        ]);

        $response->assertJsonValidationErrorFor('email');
    });

    test('with invalid role value', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/users/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'SUPERADMIN',
        ]);

        $response->assertJsonValidationErrorFor('role');
    });
});

describe('can update user', function () {
    test('with admin role', function () {
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
});
