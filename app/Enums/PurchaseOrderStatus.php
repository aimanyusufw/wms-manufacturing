<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PurchaseOrderStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case PARTIALLY_RECEIVED = 'partially_received';
    case FULLY_RECEIVED = 'fully_received';
    case CANCELLED = 'cancelled';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::PARTIALLY_RECEIVED => 'Partially Received',
            self::FULLY_RECEIVED => 'Fully Received',
            self::CANCELLED => 'Cancelled',
            self::CLOSED => 'Closed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::APPROVED => 'indigo',
            self::PARTIALLY_RECEIVED => 'orange',
            self::FULLY_RECEIVED => 'green',
            self::CANCELLED => 'red',
            self::CLOSED => 'slate',
        };
    }
}
