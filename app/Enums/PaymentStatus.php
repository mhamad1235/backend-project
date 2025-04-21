<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PENDING = 0;
    case PAID    = 1;
    case UNPAID  = 2;
    case CANCELED = 3;

    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isUnpaid(): bool
    {
        return $this === self::UNPAID;
    }

    public function isCanceled(): bool
    {
        return $this === self::CANCELED;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PENDING => __("api.pending"),
            self::PAID    => __("api.paid"),
            self::UNPAID  => __("api.unpaid"),
            self::CANCELED => __("api.canceled"),
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::PENDING => "bg-warning-subtle text-warning",
            self::PAID    => "bg-success-subtle text-success",
            self::UNPAID  => "bg-danger-subtle text-danger",
            self::CANCELED => "bg-dark-subtle text-dark",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge fs-12  {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }
}
