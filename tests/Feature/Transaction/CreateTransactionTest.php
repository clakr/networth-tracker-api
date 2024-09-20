<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot create transactions
 * - [x] admins cannot create transactions
 * - [x] cannot create transaction with empty data
 * - [x] cannot create transaction with invalid user id
 * - [x] cannot create transaction with invalid category id
 * - [x] users can create transactions
 * - [x] can create transaction without description value
 */

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

test('unauthenticated users cannot create transactions', function () {
    $transactionData = Transaction::factory()
        ->for(Category::factory()->income())
        ->make();

    $response = $this->postJson('/api/transactions', [
        'user_id' => $transactionData->user_id,
        'category_id' => $transactionData->category_id,
        'amount' => $transactionData->amount,
        'description' => $transactionData->description,
    ]);

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('admins cannot create transactions', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $transactionData = Transaction::factory()
        ->for($authedAdmin)
        ->for(Category::factory()->income())
        ->make();

    $response = $this->actingAs($authedAdmin)->postJson('/api/transactions', [
        'user_id' => $transactionData->user_id,
        'category_id' => $transactionData->category_id,
        'amount' => $transactionData->amount,
        'description' => $transactionData->description,
    ]);

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('cannot create transaction with empty data', function () {
    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->postJson('/api/transactions');

    $response->assertJsonValidationErrors(['user_id', 'category_id', 'amount']);

    $this->assertAuthenticated();
});

test('cannot create transaction with invalid user id', function () {
    $authedUser = User::factory()->make();

    $transactionData = Transaction::factory()
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->make();

    $response = $this->actingAs($authedUser)->postJson('/api/transactions', [
        'user_id' => 0,
        'category_id' => $transactionData->category_id,
        'amount' => $transactionData->amount,
        'description' => $transactionData->description,
    ]);

    $response->assertJsonValidationErrorFor('user_id');

    $this->assertAuthenticated();
});

test('cannot create transaction with invalid category id', function () {
    $authedUser = User::factory()->make();

    $transactionData = Transaction::factory()
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->make();

    $response = $this->actingAs($authedUser)->postJson('/api/transactions', [
        'user_id' => $transactionData->user_id,
        'category_id' => 0,
        'amount' => $transactionData->amount,
        'description' => $transactionData->description,
    ]);

    $response->assertJsonValidationErrorFor('category_id');

    $this->assertAuthenticated();
});

test('users can create transactions', function () {
    $authedUser = User::factory()->create();

    $transactionData = Transaction::factory()
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->make();

    $requestBody = [
        'user_id' => $transactionData->user_id,
        'category_id' => $transactionData->category_id,
        'amount' => $transactionData->amount,
        'description' => $transactionData->description,
    ];

    $response = $this->actingAs($authedUser)->postJson('/api/transactions', $requestBody);

    $response->assertCreated()
        ->assertExactJsonStructure([
            'message',
            'data' => self::TRANSACTION_RESOURCE_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Create Transaction']);

    $this->assertAuthenticated()->assertDatabaseHas('transactions', $requestBody);
});

test('can create transaction without description value', function () {
    $authedUser = User::factory()->create();

    $transactionData = Transaction::factory()
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->make();

    $requestBody = [
        'user_id' => $transactionData->user_id,
        'category_id' => $transactionData->category_id,
        'amount' => $transactionData->amount,
    ];

    $this->actingAs($authedUser)->postJson('/api/transactions', $requestBody);

    $this->assertAuthenticated()->assertDatabaseHas('transactions', $requestBody);
});
