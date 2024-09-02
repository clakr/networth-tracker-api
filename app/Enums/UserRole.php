<?php

namespace App\Enums;

use App\Traits\CustomEnumMethods;

enum UserRole: string
{
    use CustomEnumMethods;

    case USER = 'user';
    case ADMIN = 'admin';
}
