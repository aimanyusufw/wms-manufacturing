<?php

namespace App\Filament\Resources\Uoms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(30),
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                TextInput::make('decimal_places')
                    ->required()
                    ->integer()
                    ->minValue(0)
                    ->maxValue(10)
                    ->unique(ignoreRecord: true),
            ]);
    }
}
