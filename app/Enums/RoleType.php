<?php

namespace App\Enums;

enum RoleType:string
{
    case Hotel = 'hotel';
    case Tourist = 'tourist';
    case Restaurant = 'restaurant';
     public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
