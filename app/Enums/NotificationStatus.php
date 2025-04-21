<?php

namespace App\Enums;

enum NotificationStatus: int
{
    case PENDING = 0;
    case SENT     = 1;
    case FAILED   = 2;

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isSent(): bool
    {
        return $this === self::SENT;
    }

    public function isFailed(): bool
    {
        return $this === self::FAILED;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PENDING => "Pending",
            self::SENT    => "Sent",
            self::FAILED  => "Failed",
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::PENDING => "bg-warning-subtle text-warning",
            self::SENT    => "bg-success-subtle text-success",
            self::FAILED  => "bg-danger-subtle text-danger",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }
}
