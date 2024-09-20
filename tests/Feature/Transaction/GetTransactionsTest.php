<?php

/**
 * CASES:
 * - [x] unauthenticated users cannot fetch transactions
 * - [x] cannot fetch without user query parameters
 * - [x] users cannot fetch other users' transactions
 * - [x] users can fetch their transactions
 * - [x] can fetch next set of transactions
 */

use App\Exceptions\MissingUserQueryParametersException;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Exceptions;

test('unauthenticated users cannot fetch transactions', function () {
    $response = $this->get('/api/transactions');

    $response->assertUnauthorized();

    $this->assertGuest();
});

test('cannot fetch without user query parameters', function () {
    Exceptions::fake();

    $authedUser = User::factory()->make();

    $response = $this->actingAs($authedUser)->get('/api/transactions');

    $response->assertBadRequest()
        ->assertExactJsonStructure([
            'message',
        ])
        ->assertJsonFragment(['message' => 'ERROR: Missing `user` Query Parameters']);

    $this->assertAuthenticated();

    Exceptions::assertReported(MissingUserQueryParametersException::class);
});

test('users cannot fetch other users\' transactions', function () {
    $authedUser = User::factory()->create();

    $user = User::factory()->create();

    Transaction::factory(5)
        ->for($user)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedUser)->get("/api/transactions?user={$user->id}");

    $response->assertForbidden();

    $this->assertAuthenticated();
});

test('users can fetch their transactions', function () {
    $authedUser = User::factory()->create();

    Transaction::factory(5)
        ->for($authedUser)
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedUser)->get("/api/transactions?user={$authedUser->id}");

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

    $response = $this->actingAs($authedUser)->get("/api/transactions?page=2&user={$authedUser->id}");

    $response->assertJsonCount(5, 'data');

    $this->assertAuthenticated();
});
