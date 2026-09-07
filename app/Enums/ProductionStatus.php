<?php

namespace App\Enums;

enum ProductionStatus: string
{
    case DRAFT = 'draft';
    case PLANNED = 'planned';
    case RELEASED = 'released';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PLANNED => 'Planned',
            self::RELEASED => 'Released',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PLANNED => 'blue',
            self::RELEASED => 'indigo',
            self::IN_PROGRESS => 'orange',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
