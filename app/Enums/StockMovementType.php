<?php

namespace App\Enums;

enum StockMovementType: string
{
    case RECEIPT = 'receipt';
    case ISSUE = 'issue';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
    case PRODUCTION_RECEIPT = 'production_receipt';
    case PRODUCTION_ISSUE = 'production_issue';
    case RETURN_IN = 'return_in';
    case RETURN_OUT = 'return_out';
    case SHIPMENT = 'shipment';

    public function label(): string
    {
        return match ($this) {
            self::RECEIPT => 'Receipt',
            self::ISSUE => 'Issue',
            self::TRANSFER_IN => 'Transfer In',
            self::TRANSFER_OUT => 'Transfer Out',
            self::ADJUSTMENT_IN => 'Adjustment In',
            self::ADJUSTMENT_OUT => 'Adjustment Out',
            self::PRODUCTION_RECEIPT => 'Production Receipt',
            self::PRODUCTION_ISSUE => 'Production Issue',
            self::RETURN_IN => 'Return In',
            self::RETURN_OUT => 'Return Out',
            self::SHIPMENT => 'Shipment',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RECEIPT => 'green',
            self::ISSUE => 'red',
            self::TRANSFER_IN => 'blue',
            self::TRANSFER_OUT => 'orange',
            self::ADJUSTMENT_IN => 'cyan',
            self::ADJUSTMENT_OUT => 'amber',
            self::PRODUCTION_RECEIPT => 'emerald',
            self::PRODUCTION_ISSUE => 'rose',
            self::RETURN_IN => 'indigo',
            self::RETURN_OUT => 'pink',
            self::SHIPMENT => 'purple',
        };
    }

    public function direction(): MovementDirection
    {
        return match ($this) {
            self::RECEIPT, self::TRANSFER_IN, self::ADJUSTMENT_IN, self::PRODUCTION_RECEIPT, self::RETURN_IN => MovementDirection::IN,
            self::ISSUE, self::TRANSFER_OUT, self::ADJUSTMENT_OUT, self::PRODUCTION_ISSUE, self::RETURN_OUT, self::SHIPMENT => MovementDirection::OUT,
        };
    }
}
