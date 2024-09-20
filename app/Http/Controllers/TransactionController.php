<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingUserQueryParametersException;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = request()->query('user');
        if (! $userId) {
            throw new MissingUserQueryParametersException;
        }

        $user = User::findOrFail($userId);

        Gate::allowIf(fn (User $authedUser) => $authedUser->id === $user->id);

        $transactions = $user->transactions()->paginate();

        return TransactionResource::collection($transactions)->additional(['message' => 'SUCCESS: Get Transactions']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $transaction = Transaction::create([
            'user_id' => $request->input('user_id'),
            'category_id' => $request->input('category_id'),
            'amount' => $request->input('amount'),
            'description' => $request->input('description'),
        ]);

        return response([
            'data' => new TransactionResource($transaction),
            'message' => 'SUCCESS: Create Transaction',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
