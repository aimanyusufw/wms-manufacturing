<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductType: string implements HasLabel, HasColor
{
    case RAW_MATERIAL = 'raw_material';
    case WIP = 'wip';
    case FINISHED_GOOD = 'finished_good';
    case CONSUMABLE = 'consumable';
    case SPARE_PART = 'spare_part';

    public function getLabel(): string
    {
        return match ($this) {
            self::RAW_MATERIAL => 'Raw Material',
            self::WIP => 'Work In Progress',
            self::FINISHED_GOOD => 'Finished Good',
            self::CONSUMABLE => 'Consumable',
            self::SPARE_PART => 'Spare Part',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::RAW_MATERIAL => 'danger',
            self::WIP => 'warning',
            self::FINISHED_GOOD => 'success',
            self::CONSUMABLE => 'gray',
            self::SPARE_PART => 'info',
        };
    }
}
