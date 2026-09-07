<?php

namespace App\Enums;

enum InventoryStatus: string
{
    case AVAILABLE = 'available';
    case QUARANTINE = 'quarantine';
    case BLOCKED = 'blocked';
    case DAMAGED = 'damaged';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::QUARANTINE => 'Quarantine',
            self::BLOCKED => 'Blocked',
            self::DAMAGED => 'Damaged',
            self::EXPIRED => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'green',
            self::QUARANTINE => 'yellow',
            self::BLOCKED => 'red',
            self::DAMAGED => 'orange',
            self::EXPIRED => 'gray',
        };
    }
}
