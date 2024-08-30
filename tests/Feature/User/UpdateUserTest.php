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

    test('with user role', function () {
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

    test('with empty data', function () {
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

    test('with invalid email', function () {
        $admin = User::factory()->admin()->create();

        $existingUser = User::factory()->create();
        $user = User::factory()->make();

        $requestBody = [
            'name' => $user->name,
            'email' => 'THIS IS INVALID EMAIL',
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

    test('with not unique email', function () {
        $admin = User::factory()->admin()->create();

        $existingUser = User::factory()->create();
        $user = User::factory()->make();

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

    test('with invalid role value', function () {
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

        $this->assertAuthenticated()
            ->assertDatabaseHas('users', $requestBody)
            ->assertDatabaseMissing('users', [
                'name' => $existingUser->name,
                'email' => $existingUser->email,
                'role' => $existingUser->role,
            ]);
    });
});
