<?php

namespace App\Enums;

enum SalesOrderStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case PARTIALLY_ALLOCATED = 'partially_allocated';
    case PARTIALLY_SHIPPED = 'partially_shipped';
    case FULLY_SHIPPED = 'fully_shipped';
    case CANCELLED = 'cancelled';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::PARTIALLY_ALLOCATED => 'Partially Allocated',
            self::PARTIALLY_SHIPPED => 'Partially Shipped',
            self::FULLY_SHIPPED => 'Fully Shipped',
            self::CANCELLED => 'Cancelled',
            self::CLOSED => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::APPROVED => 'indigo',
            self::PARTIALLY_ALLOCATED => 'amber',
            self::PARTIALLY_SHIPPED => 'orange',
            self::FULLY_SHIPPED => 'green',
            self::CANCELLED => 'red',
            self::CLOSED => 'slate',
        };
    }
}
