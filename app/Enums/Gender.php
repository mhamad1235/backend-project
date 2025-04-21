<?php

namespace App\Enums;

enum Gender: int
{
    case MALE   = 0;
    case FEMALE = 1;
    case OTHER  = 2;

    public function isOther(): bool
    {
        return $this === self::OTHER;
    }

    public function isMale(): bool
    {
        return $this === self::MALE;
    }

    public function isFemale(): bool
    {
        return $this === self::FEMALE;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::OTHER  => 'Other',
            self::MALE   => 'Male',
            self::FEMALE => 'Female',
        };
    }

    private function getLabelColor(): string
    {
        return match ($this) {
            self::OTHER  => "bg-info-subtle text-info",
            self::MALE   => "bg-secondary-subtle text-secondary",
            self::FEMALE => "bg-secondary-subtle text-secondary",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge fs-12  {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }

    public static function toArraySelect(): array
    {
        return [
            ['id' => self::OTHER, 'name' => 'Other'],
            ['id' => self::MALE, 'name' => 'Male'],
            ['id' => self::FEMALE, 'name' => "Female"]
        ];
    }
}
