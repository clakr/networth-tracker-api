<?php

namespace App\Http\Controllers;

use App\Exceptions\MissingUserQueryParametersException;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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

        Gate::authorize('viewOwn', $user);

        $transactions = $user->transactions()->paginate();

        return TransactionResource::collection($transactions)->additional(['message' => 'SUCCESS: Get Transactions']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        //
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
