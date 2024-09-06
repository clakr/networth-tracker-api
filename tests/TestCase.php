<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    const PAGINATION_KEYS = [
        'links' => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'links' => [
                '*' => [
                    'url',
                    'label',
                    'active',
                ],
            ],
            'path',
            'per_page',
            'to',
            'total',
        ],
    ];

    const USER_RESOURCE_KEYS = [
        'id',
        'name',
        'email',
        'emailVerifiedAt',
        'role',
        'createdAt',
        'updatedAt',
    ];

    const CATEGORY_RESOURCE_KEYS = [
        'id',
        'name',
        'type',
        'createdAt',
        'updatedAt',
    ];

    const SUBCATEGORY_RESOURCE_KEYS = [
        'id',
        'name',
        'createdAt',
        'updatedAt',
    ];
}
