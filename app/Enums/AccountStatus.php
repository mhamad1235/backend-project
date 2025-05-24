<?php

namespace App\Enums;

enum AccountStatus:string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';

      public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
 public function label(): string
    {
        return match($this) {
            self::Active => 'Active',
            self::Inactive => 'Not Active',
            self::Suspended => 'Temporarily Blocked',
        };
    }
}
