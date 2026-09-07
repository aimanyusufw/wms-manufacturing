<?php

namespace App\Enums;

enum OpnameStatus: string
{
    case DRAFT = 'draft';
    case COUNTING = 'counting';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::COUNTING => 'Counting',
            self::COMPLETED => 'Completed',
            self::APPROVED => 'Approved',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::COUNTING => 'orange',
            self::COMPLETED => 'blue',
            self::APPROVED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
