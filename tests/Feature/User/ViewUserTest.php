<?php

/**
 * CASES
 * - [x] unauthenticated users cannot fetch any users
 * - [x] users cannot fetch other users
 * - [x] users can fetch themselves
 * - [x] admins can fetch all users
 */

use App\Models\User;

test('cannot fetch users without authentication', function () {
    $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users cannot fetch other users data', function () {
    $otherUser = User::factory()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/users/{$otherUser->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('users can fetch their own data', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertExactJsonStructure([
            'data' => self::USER_RESOURCE_KEYS,
            'message',
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get User']);

    $this->assertAuthenticated();
});

test('admins can fetch any users data', function () {
    $admin = User::factory()->admin()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($admin)->getJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertExactJsonStructure([
            'data' => self::USER_RESOURCE_KEYS,
            'message',
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get User']);

    $this->assertAuthenticated();
});
