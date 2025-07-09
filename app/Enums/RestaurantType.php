<?php

namespace App\Enums;

enum RestaurantType: string
{
    case Cabin = 'cabin';
    case Stay = 'stay';
    case Lake = 'lake';
    case River = 'river';

    public function label(): string
    {
        return match ($this) {
            self::Cabin => 'Cabin',
            self::Stay => 'Stay',
            self::Lake => 'Lake',
            self::River => 'River',
        };
    }

    public static function options(): array
    {
        return array_map(fn($type) => ['value' => $type->value, 'label' => $type->label()], self::cases());
    }
}
