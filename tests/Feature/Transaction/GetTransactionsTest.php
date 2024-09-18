<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot fetch transactions
 * - [x] users can fetch their transactions
 * - [ ] can fetch next set of transactions
 */

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

test('unauthenticated users cannot fetch transactions', function () {
    $response = $this->get('/api/transactions');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('users can fetch their transactions', function () {
    $authedUser = User::factory()->create();

    Transaction::factory(5)
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedUser)->get('/api/transactions');

    $response->assertOk()
        ->assertExactJsonStructure([
            'message',
            'data' => ['*' => self::TRANSACTION_RESOURCE_KEYS],
            ...self::PAGINATION_KEYS,
        ])
        ->assertJsonFragment(['message' => 'SUCCESS: Get Transactions'])
        ->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});

test('can fetch next set of transactions', function () {
    $authedUser = User::factory()->create();

    Transaction::factory(15)
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedUser)->get('/api/transactions?page=2');

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
