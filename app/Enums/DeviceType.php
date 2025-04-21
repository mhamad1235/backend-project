<?php

namespace App\Enums;

enum DeviceType: int
{
    case MOBILE   = 0;
    case WEB      = 1;

    public function isMobile(): bool
    {
        return $this === self::MOBILE;
    }

    public function isWeb(): bool
    {
        return $this === self::WEB;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::MOBILE => 'Mobile',
            self::WEB    => 'Web',
        };
    }
}
