<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasLabel, HasColor
{
    case Receiving = 'receiving';
    case QC = 'qc';
    case Zone = 'zone';
    case Aisle = 'aisle';
    case Rack = 'rack';
    case Shelf = 'shelf';
    case Bin = 'bin';
    case Staging = 'staging';
    case Shipping = 'shipping';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Receiving => 'Receiving Area',
            self::QC => 'QC Area',
            self::Zone => 'Zone',
            self::Aisle => 'Aisle',
            self::Rack => 'Rack',
            self::Shelf => 'Shelf',
            self::Bin => 'Bin',
            self::Staging => 'Staging Area',
            self::Shipping => 'Shipping Area',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Receiving => 'info',
            self::QC => 'warning',
            self::Zone => 'primary',
            self::Aisle => 'gray',
            self::Rack => 'success',
            self::Shelf => 'info',
            self::Bin => 'success',
            self::Staging => 'warning',
            self::Shipping => 'danger',
        };
    }
}
