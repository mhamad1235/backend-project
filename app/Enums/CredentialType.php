<?php

namespace App\Enums;

enum CredentialType: int
{
    case EMAIL   = "email";
    case PHONE   = "phone";


    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
