<?php

namespace App;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';

    public static function validationRules(): string
    {
        $roles = array_reduce(UserRole::cases(), fn ($previous, $role) => $previous ? $previous.','.$role->value : $role->value, '');

        return "in:{$roles}";
    }
}
