<?php

namespace App\Enums;

enum ActiveStatus: int
{
    case DISABLED   = 0;
    case ACTIVE     = 1;

    public function isDisabled(): bool
    {
        return $this === self::DISABLED;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::DISABLED => 'Disabled',
            self::ACTIVE   => 'Active',
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::DISABLED => "bg-danger-subtle text-danger",
            self::ACTIVE   => "bg-success-subtle text-success",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge fs-12  {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }
}
