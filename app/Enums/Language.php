<?php

namespace App\Enums;

enum Language: int
{
    case ENGLISH = 1;
    case ARABIC  = 2;
    case KURDISH = 3;

    public function isEnglish(): bool
    {
        return $this === self::ENGLISH;
    }

    public function isArabic(): bool
    {
        return $this === self::ARABIC;
    }

    public function isKurdish(): bool
    {
        return $this === self::KURDISH;
    }

    public function getDirection(): string
    {
        return match ($this) {
            self::ARABIC  => 'rtl',
            default       => 'ltr',
        };
    }

    public function getLocale(): string
    {
        return match ($this) {
            self::ENGLISH => 'en_US',
            self::ARABIC  => 'ar_AE',
            self::KURDISH => 'ku_IQ',
        };
    }

    public function getFlag(): string
    {
        return match ($this) {
            self::ENGLISH => 'us',
            self::ARABIC  => 'ae',
            self::KURDISH => 'iq',
        };
    }

    public function getShortLabel(): string
    {
        return match ($this) {
            self::ENGLISH => "en",
            self::ARABIC  => "ar",
            self::KURDISH => "ku",
        };
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::ENGLISH => 'English',
            self::ARABIC  => 'العربية',
            self::KURDISH => 'کوردی',
        };
    }

    public static function getArrayOfShortLabels(): array
    {
        return [
            self::ENGLISH->getShortLabel(),
            self::ARABIC->getShortLabel(),
            self::KURDISH->getShortLabel(),
        ];
    }

    public static function fromString(string $lang): self
    {
        return match ($lang) {
            "en" => self::ENGLISH,
            "ar" => self::ARABIC,
            "ku" => self::KURDISH,
            default => self::ENGLISH,
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::ENGLISH => "badge-soft-primary",
            self::ARABIC  => "badge-soft-info",
            self::KURDISH => "badge-soft-success",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge badge-pill font-size-13 p-2 {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }
}
