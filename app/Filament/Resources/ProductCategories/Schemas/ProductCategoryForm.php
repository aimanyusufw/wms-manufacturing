<?php

namespace App\Filament\Resources\ProductCategories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProductCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->relationship(
                        name: 'parent',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->whereNull('parent_id')
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty if this is a main category'),

                TextInput::make('code')
                    ->label('Category Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. ELECTRONICS')
                    ->maxLength(50),

                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->placeholder('e.g. Electronics')
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Description')
                    ->nullable()
                    ->placeholder('Describe this category...'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}
