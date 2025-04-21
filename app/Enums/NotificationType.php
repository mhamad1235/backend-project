<?php

namespace App\Enums;

enum NotificationType: int
{
    case PUSH = 1;
    case IN_APP = 2;
    case INBOX = 3;

    public function isPush(): bool
    {
        return $this === self::PUSH;
    }

    public function isInApp(): bool
    {
        return $this === self::IN_APP;
    }

    public function isInbox(): bool
    {
        return $this === self::INBOX;
    }

    public function getLabelText(): string
    {
        return match ($this) {
            self::PUSH => "Push",
            self::IN_APP => "In-App",
            self::INBOX => "Inbox",
        };
    }

    public function getLabelColor(): string
    {
        return match ($this) {
            self::PUSH => "bg-primary-subtle text-primary",
            self::IN_APP => "bg-info-subtle text-info",
            self::INBOX => "bg-secondary-subtle text-secondary",
        };
    }

    public function getHtmlLabel(): string
    {
        return "<span class='badge {$this->getLabelColor()}'>{$this->getLabelText()}</span>";
    }
}
