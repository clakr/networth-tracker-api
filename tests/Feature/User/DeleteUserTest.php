<?php

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users cannot delete any user
 * - [x] admin cannot delete own account
 * - [x] admin can delete any user
 */

use App\Models\User;

test('cannot delete user without authentication', function () {
    $user = User::factory()->create();

    $response = $this->deleteJson("/api/users/{$user->id}");

    $response->assertUnauthorized();

    $this->assertGuest()
        ->assertModelExists($user);
});

test('user cannot delete any user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs(User::factory()->create())->deleteJson("/api/users/{$user->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertModelExists($user);
});

test('admin cannot delete own account', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/users/{$admin->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()
        ->assertModelExists($admin);
});

test('admin can delete any user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete User']);

    $this->assertAuthenticated()
        ->assertSoftDeleted($user);
});
