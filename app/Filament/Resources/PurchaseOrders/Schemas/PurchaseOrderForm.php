<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Enums\PurchaseOrderStatus;
use App\Models\ProductUom;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Wezlo\FilamentApproval\Infolists\ApprovalStatusSection;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order details')
                    ->description('Define the supplier, destination warehouse, and purchasing dates.')
                    ->schema([
                        TextInput::make('document_number')
                            ->label('PO number')
                            ->placeholder('PO-2026-0001')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('warehouse_id')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        DatePicker::make('order_date')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        DatePicker::make('expected_date')
                            ->native(false)
                            ->afterOrEqual('order_date')
                            ->helperText('Must be on or after the order date.'),
                        Select::make('status')
                            ->options(PurchaseOrderStatus::class)
                            ->default(PurchaseOrderStatus::DRAFT)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Textarea::make('notes')
                            ->placeholder('Add delivery instructions or internal notes...')
                            ->rows(4)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Audit information')
                    ->schema([
                        Placeholder::make('creator.name')
                            ->label('Created by')
                            ->content(fn($record): HtmlString => new HtmlString(e($record?->creator?->name ?? '-'))),
                        Placeholder::make('approver.name')
                            ->label('Approved by')
                            ->content(fn($record): HtmlString => new HtmlString(e($record?->approver?->name ?? '-'))),
                        Placeholder::make('approved_at')
                            ->label('Approved at')
                            ->content(fn($record): string => $record?->approved_at?->format('d M Y, H:i') ?? '-'),
                        Placeholder::make('created_at')
                            ->label('Created')
                            ->content(fn($record): string => $record?->created_at?->format('d M Y, H:i') ?? '-'),
                    ])
                    ->visible(fn(string $operation): bool => $operation === 'edit')
                    ->columns(2),
                Section::make('Order items')
                    ->description('Add the products, purchase units, and quantities for this order.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->label('Items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->relationship(
                                        'product',
                                        'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('is_active', true),
                                    )
                                    ->searchable(['name', 'sku'])
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('uom_id')
                                    ->label('Purchase UOM')
                                    ->options(fn(Get $get): array => ProductUom::query()
                                        ->with('uom')
                                        ->where('product_id', $get('product_id'))
                                        ->where('is_purchase_uom', true)
                                        ->get()
                                        ->mapWithKeys(fn(ProductUom $productUom): array => [
                                            $productUom->uom_id => $productUom->uom->name . ' (' . $productUom->uom->code . ')',
                                        ])
                                        ->all())
                                    ->disabled(fn(Get $get): bool => blank($get('product_id')))
                                    ->required(),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->minValue(0.000001)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('unit_price')
                                    ->label('Unit price')
                                    ->numeric()
                                    ->minValue(0)
                                    ->prefix('Rp')
                                    ->required(),
                                TextInput::make('discount_amount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->prefix('Rp'),
                                TextInput::make('tax_amount')
                                    ->label('Tax')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->prefix('Rp'),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Add product')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => filled($state['product_id'] ?? null)
                                ? 'Product #' . ($state['product_id'])
                                : 'New item')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
