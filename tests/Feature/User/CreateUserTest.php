<?php

use App\Models\User;

test('unauthenticated users cannot create resource', function () {
    $user = User::factory()->make();

    $response = $this->postJson('/api/users', [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'password' => $user->password,
    ]);

    $this->assertGuest();

    $response->assertUnauthorized();
});

test('users with user role cannot create resource', function () {
    $user = User::factory()->make();

    $response = $this->actingAs(User::factory()->create())
        ->postJson('/api/users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => $user->password,
        ]);

    $this->assertAuthenticated();

    $response->assertForbidden();
});

test('admin can create resource', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->make();

    $requestBody = [
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'password' => $user->password,
    ];

    $response = $this->actingAs($admin)->postJson('/api/users', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::USER_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create User']);

    $this->assertDatabaseHas('users', $requestBody);
});

test('can not create user with invalid data', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->postJson('/api/users');

    $response->assertJsonValidationErrors(['name', 'email']);
});

test('can not create user with not unique email', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->postJson('/api/users', [
        'name' => $user->name,
        'email' => $admin->email,
        'role' => $user->role,
        'password' => $user->password,
    ]);

    $response->assertJsonValidationErrorFor('email');
});

test('can create user successfully without role and password specified', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $requestBody = [
        'name' => $user->name,
        'email' => $user->email,
    ];

    $this->actingAs($admin)->postJson('/api/users', $requestBody);

    $this->assertDatabaseHas('users', $requestBody);
});
