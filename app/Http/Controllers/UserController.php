<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection(User::paginate())
            ->additional(['message' => 'SUCCESS: Get Users']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => $request->input('password', 'password'),
        ]);

        return response([
            'data' => new UserResource($user),
            'message' => 'SUCCESS: Create User',
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', [
            User::class,
            $user,
        ]);

        return response([
            'data' => new UserResource($user),
            'message' => 'SUCCESS: Get User',
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user = tap($user)->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
        ]);

        return response([
            'data' => new UserResource($user),
            'message' => 'SUCCESS: Update User',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', [
            User::class,
            $user,
        ]);

        $user->delete();

        return response([
            'message' => 'SUCCESS: Delete User',
        ], Response::HTTP_OK);
    }
}
