<?php

use App\Models\User;

describe('POST: /api/users', function () {
    test('asserts response status is `HTTP_UNAUTHORIZED` if user is not authenticated', function () {
        $user = User::factory()->make(); // `make()` method is used instead of `create()` since we only care for the values of the User model

        $response = $this->postJson('/api/users', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'password' => $user->password,
        ]);

        $response->assertUnauthorized();
    });

    test("asserts response status is `HTTP_FORBIDDEN` if user's role is user", function () {
        $userCreated = User::factory()->create(); // this User instance is store in the database
        $user = User::factory()->make();

        $response = $this->actingAs($userCreated)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password' => $user->password,
            ]);

        $response->assertForbidden();
    });

    test("asserts response status is `HTTP_OK` if user's role is admin", function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password' => $user->password,
            ]);

        $response->assertCreated();
    });

    test('asserts validation errors if invalid data are sent', function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->postJson('/api/users');

        $response->assertJsonValidationErrors(['name', 'email']);
    });

    test("asserts validation error if request's email value is not unique", function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $admin->email,
                'role' => $user->role,
                'password' => $user->password,
            ]);

        $response->assertJsonValidationErrorFor('email');
    });

    test('asserts user is created with default role and password values, even these keys are not specified', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $response->assertCreated();
    });

    test('asserts the correct JSON structure', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password' => $user->password,
            ]);

        $response->assertExactJsonStructure([
            'data' => self::USER_RESOURCE_KEYS,
            'message',
        ]);
    });

    test('asserts the value of the JSON `message` key', function () {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->make();

        $response = $this->actingAs($admin)
            ->postJson('/api/users', [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password' => $user->password,
            ]);

        $response->assertJsonFragment(['message' => 'SUCCESS: Create User']);
    });
});
