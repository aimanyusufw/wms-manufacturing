<?php

namespace App\Enums;

enum PickingStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case PICKING = 'picking';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ASSIGNED => 'Assigned',
            self::PICKING => 'Picking',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::ASSIGNED => 'blue',
            self::PICKING => 'orange',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
