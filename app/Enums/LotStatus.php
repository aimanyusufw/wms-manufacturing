<?php

namespace App\Enums;

enum LotStatus: string
{
    case ACTIVE = 'active';
    case QUARANTINE = 'quarantine';
    case BLOCKED = 'blocked';
    case EXPIRED = 'expired';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::QUARANTINE => 'Quarantine',
            self::BLOCKED => 'Blocked',
            self::EXPIRED => 'Expired',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::QUARANTINE => 'yellow',
            self::BLOCKED => 'red',
            self::EXPIRED => 'gray',
            self::CLOSED => 'slate',
        };
    }
}
