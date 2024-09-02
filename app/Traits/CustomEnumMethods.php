<?php

namespace App\Traits;

trait CustomEnumMethods
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function validationRules(): string
    {
        $values = array_reduce(
            self::values(),
            fn ($previous, $value) => $previous ? "{$previous},{$value}" : $value,
            ''
        );

        return "in:{$values}";
    }
}
