<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case READY = 'ready';
    case LOADING = 'loading';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::READY => 'Ready',
            self::LOADING => 'Loading',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::READY => 'blue',
            self::LOADING => 'orange',
            self::SHIPPED => 'purple',
            self::DELIVERED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
