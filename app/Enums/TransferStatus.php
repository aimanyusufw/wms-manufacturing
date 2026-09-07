<?php

namespace App\Enums;

enum TransferStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case IN_TRANSIT = 'in_transit';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::IN_TRANSIT => 'In Transit',
            self::RECEIVED => 'Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::APPROVED => 'indigo',
            self::IN_TRANSIT => 'orange',
            self::RECEIVED => 'green',
            self::CANCELLED => 'red',
        };
    }
}
