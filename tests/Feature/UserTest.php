<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

describe('GET: /api/users', function () {
    it("should return `HTTP_FORBIDDEN` response status if user's role is user", function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        $response->assertStatus(Response::HTTP_OK);
    });

    it("should return `HTTP_OK` response status if user's role is admin", function () {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/api/users');

        $response->assertStatus(Response::HTTP_OK);
    });

});
