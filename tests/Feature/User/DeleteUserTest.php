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

    $this->assertGuest()->assertModelExists($user);
});

test('user cannot delete any user', function () {
    $authedUser = User::factory()->create();

    $user = User::factory()->create();

    $response = $this->actingAs($authedUser)->deleteJson("/api/users/{$user->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelExists($user);
});

test('admin cannot delete own account', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->create();

    $response = $this->actingAs($authedAdmin)->deleteJson("/api/users/{$authedAdmin->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelExists($authedAdmin);
});

test('admin can delete any user', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $user = User::factory()->create();

    $response = $this->actingAs($authedAdmin)->deleteJson("/api/users/{$user->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete User']);

    $this->assertAuthenticated()->assertSoftDeleted($user);
});
