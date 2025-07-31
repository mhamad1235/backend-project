<?php

namespace App\Enums;

enum RoleType:string
{
    case Hotel = 'hotel';
    case Motel = 'motel';
    case Agency = 'agency';
    case Tourist = 'tourist';
     public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
