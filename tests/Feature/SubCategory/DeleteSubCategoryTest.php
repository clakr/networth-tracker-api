<?php

/**
 * CASES:
 * - [x] unauthenticated users
 * - [x] users cannot delete sub categories
 * - [x] admins can delete sub categories
 */

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;

test('unauthenticated users cannot delete sub categories', function () {
    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $response = $this->deleteJson("/api/subcategories/{$subCategory->id}");

    $response->assertUnauthorized();

    $this->assertGuest()->assertModelExists($subCategory);
});

test('users cannot delete sub categories', function () {
    $authedUser = User::factory()->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedUser)->deleteJson("/api/subcategories/{$subCategory->id}");

    $response->assertForbidden();

    $this->assertAuthenticated()->assertModelExists($subCategory);
});

test('admins can delete sub categories', function () {
    $authedAdmin = User::factory()
        ->admin()
        ->make();

    $subCategory = SubCategory::factory()
        ->for(Category::factory()->income())
        ->create();

    $response = $this->actingAs($authedAdmin)->deleteJson("/api/subcategories/{$subCategory->id}");

    $response->assertOk()
        ->assertExactJsonStructure(['message'])
        ->assertJsonFragment(['message' => 'SUCCESS: Delete Subcategory']);

    $this->assertAuthenticated()->assertModelMissing($subCategory);
});
