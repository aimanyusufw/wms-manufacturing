<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QcStatus: string implements HasLabel, HasColor
{
    case PENDING = 'pending';
    case PASSED = 'passed';
    case FAILED = 'failed';
    case PARTIALLY_PASSED = 'partially_passed';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::PASSED => 'Passed',
            self::FAILED => 'Failed',
            self::PARTIALLY_PASSED => 'Partially Passed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::PASSED => 'green',
            self::FAILED => 'red',
            self::PARTIALLY_PASSED => 'orange',
        };
    }
}
