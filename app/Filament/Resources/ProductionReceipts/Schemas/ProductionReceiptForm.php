<?php

namespace App\Filament\Resources\ProductionReceipts\Schemas;

use App\Enums\DocumentStatus;
use App\Models\Location;
use App\Models\Product;
use App\Models\Uom;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProductionReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Receipt details')
                    ->description('Record finished goods received from production and the target warehouse.')
                    ->schema([
                        TextInput::make('document_number')
                            ->label('PR number')
                            ->placeholder('PR-2026-0001')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DateTimePicker::make('receipt_date')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        Select::make('status')
                            ->options(DocumentStatus::class)
                            ->default(DocumentStatus::DRAFT)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Textarea::make('notes')
                            ->placeholder('Add production or receiving notes...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Produced items')
                    ->description('Add the products, quantities, lots, pallets, and optional destination bins.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(fn(): array => Product::query()
                                        ->where('is_active', true)
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn(Product $product): array => [
                                            $product->id => $product->sku . ' - ' . $product->name,
                                        ])->all())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('uom_id')
                                    ->label('UOM')
                                    ->options(fn(Get $get): array => Uom::query()
                                        ->whereHas('productUoms', fn(Builder $query) => $query->where('product_id', $get('product_id')))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn(Get $get): bool => blank($get('product_id')))
                                    ->required(),
                                TextInput::make('received_qty')
                                    ->label('Received qty')
                                    ->numeric()
                                    ->minValue(0.000001)
                                    ->required(),
                                TextInput::make('rejected_qty')
                                    ->label('Rejected qty')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                Select::make('lot_id')
                                    ->label('Lot')
                                    ->relationship('lot', 'lot_number')
                                    ->searchable()
                                    ->preload(),
                                Select::make('pallet_id')
                                    ->label('Pallet')
                                    ->relationship('pallet', 'pallet_code')
                                    ->searchable()
                                    ->preload(),
                                Select::make('destination_bin_id')
                                    ->label('Destination bin')
                                    ->options(fn(Get $get): array => Location::query()
                                        ->where('warehouse_id', $get('../../warehouse_id'))
                                        ->where('is_active', true)
                                        ->where('is_putaway_allowed', true)
                                        ->orderBy('code')
                                        ->pluck('code', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Textarea::make('notes')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add produced item')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): string => filled($state['product_id'] ?? null)
                                ? 'Product #' . $state['product_id']
                                : 'New produced item')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
