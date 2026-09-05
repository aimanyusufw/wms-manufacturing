<?php

namespace App\Filament\Resources\QualityInspections\Schemas;

use App\Enums\QcStatus;
use App\Models\GoodsReceipt;
use App\Models\Lot;
use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class QualityInspectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inspection details')
                    ->description('Record the inspection source and quality review date.')
                    ->schema([
                        TextInput::make('inspection_number')
                            ->label('Inspection number')
                            ->placeholder('QI-2026-0001')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->required(),
                        DateTimePicker::make('inspection_date')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        Select::make('goods_receipt_id')
                            ->label('Goods receipt')
                            ->relationship('goodsReceipt', 'document_number')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Select::make('status')
                            ->options(QcStatus::class)
                            ->default(QcStatus::PENDING)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Textarea::make('notes')
                            ->placeholder('Add inspection notes or non-conformance details...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Inspection items')
                    ->description('Record inspected, passed, and failed quantities for each product.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(fn(Get $get): array => self::productOptions($get))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('lot_id')
                                    ->label('Lot')
                                    ->options(fn(Get $get): array => Lot::query()
                                        ->where('product_id', $get('product_id'))
                                        ->orderBy('lot_number')
                                        ->pluck('lot_number', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn(Get $get): bool => blank($get('product_id'))),
                                TextInput::make('inspected_qty')
                                    ->label('Inspected qty')
                                    ->numeric()
                                    ->minValue(0.000001)
                                    ->required(),
                                TextInput::make('passed_qty')
                                    ->label('Passed qty')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                TextInput::make('failed_qty')
                                    ->label('Failed qty')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                Textarea::make('remarks')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add inspection item')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): string => filled($state['product_id'] ?? null)
                                ? 'Product #' . $state['product_id']
                                : 'New inspection item')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }

    /** @return array<int|string, string> */
    private static function productOptions(Get $get): array
    {
        $goodsReceiptId = $get('../../goods_receipt_id');

        if ($goodsReceiptId) {
            return GoodsReceipt::query()
                ->with('items.product')
                ->find($goodsReceiptId)?->items()
                ->get()
                ->mapWithKeys(fn($item): array => [$item->product_id => $item->product->sku . ' - ' . $item->product->name])
                ->all() ?? [];
        }

        return Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn(Product $product): array => [$product->id => $product->sku . ' - ' . $product->name])
            ->all();
    }
}
