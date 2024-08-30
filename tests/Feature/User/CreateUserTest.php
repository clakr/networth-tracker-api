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
describe('cannot create user', function () {
    test('without authentication', function () {
        $user = User::factory()->make();

        $response = $this->postJson('/api/users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $response->assertUnauthorized();

        $this->assertGuest()
            ->assertModelMissing($user);
    });

    test('with user role', function () {
        $user = User::factory()->make();

        $response = $this->actingAs(User::factory()->create())
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $response->assertForbidden();

        $this->assertAuthenticated()
            ->assertModelMissing($user);
    });

    test('with empty data', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/api/users');

        $response->assertJsonValidationErrors(['name', 'email']);

        $this->assertAuthenticated();
    });

    test('with invalid email', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => 'THIS IS INVALID EMAIL',
            ]);

        $response->assertJsonValidationErrorFor('email');

        $this->assertAuthenticated()
            ->assertModelMissing($user);
    });

    test('with not unique email', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $admin->email,
            ]);

        $response->assertJsonValidationErrorFor('email');

        $this->assertAuthenticated()
            ->assertModelMissing($user);
    });

    test('with invalid role', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'SUPERADMIN',
            ]);

        $response->assertJsonValidationErrorFor('role');

        $this->assertAuthenticated()
            ->assertModelMissing($user);
    });
});

describe('can create user', function () {
    test('without role and password specified', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $requestBody = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $this->actingAs($admin)->postJson('/api/users', $requestBody);

        $this->assertAuthenticated()
            ->assertDatabaseHas('users', $requestBody);
    });

    test('with admin role', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $requestBody = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        $response = $this->actingAs($admin)->postJson('/api/users', $requestBody);

        $response->assertCreated()
            ->assertExactJsonStructure([
                'message',
                'data' => self::USER_RESOURCE_KEYS,
            ])
            ->assertJsonFragment(['message' => 'SUCCESS: Create User']);

        $this->assertAuthenticated()
            ->assertDatabaseHas('users', $requestBody);
    });
});
