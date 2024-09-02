<?php

namespace App\Enums;

use App\Traits\CustomEnumMethods;

enum CategoryType: string
{
    use CustomEnumMethods;

    case INCOME = 'income';
    case EXPENSE = 'expense';
}
