<?php

namespace App\Filament\Resources\GoodsReceipts\Schemas;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class GoodsReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Receipt details')
                    ->description('Record the delivery and where the goods were received.')
                    ->schema([
                        TextInput::make('document_number')
                            ->label('GRN number')
                            ->placeholder('GRN-2026-0001')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('purchase_order_id')
                            ->label('Purchase order')
                            ->relationship('purchaseOrder', 'document_number')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $set('supplier_id', $state ? PurchaseOrder::find($state)?->supplier_id : null);
                            })
                            ->nullable()
                            ->helperText('Optional for receipts without a purchase order.'),
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
                        DateTimePicker::make('receipt_date')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        TextInput::make('delivery_note_number')
                            ->label('Delivery note number')
                            ->maxLength(100),
                        Select::make('status')
                            ->options(\App\Enums\DocumentStatus::class)
                            ->default(\App\Enums\DocumentStatus::DRAFT)
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Textarea::make('notes')
                            ->placeholder('Add receiving notes or discrepancy details...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Received items')
                    ->description('Select PO lines where available, then record the physical and QC quantities.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('purchase_order_item_id')
                                    ->label('Purchase order item')
                                    ->options(fn(Get $get): array => PurchaseOrderItem::query()
                                        ->with(['product', 'uom'])
                                        ->when($get('../../purchase_order_id'), fn(Builder $query, $purchaseOrderId) => $query->where('purchase_order_id', $purchaseOrderId))
                                        ->get()
                                        ->mapWithKeys(fn(PurchaseOrderItem $item): array => [
                                            $item->id => $item->product->sku . ' - ' . $item->product->name . ' (' . $item->quantity . ' ' . $item->uom->code . ')',
                                        ])->all())
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function (?int $state, Set $set): void {
                                        $item = $state ? PurchaseOrderItem::with('product', 'uom')->find($state) : null;
                                        $set('product_id', $item?->product_id);
                                        $set('uom_id', $item?->uom_id);
                                    })
                                    ->required(fn(Get $get): bool => filled($get('../../purchase_order_id'))),
                                Hidden::make('product_id'),
                                Hidden::make('uom_id'),
                                TextInput::make('received_qty')
                                    ->label('Received qty')
                                    ->numeric()
                                    ->minValue(0.000001)
                                    ->required(),
                                TextInput::make('accepted_qty')
                                    ->label('Accepted qty')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                TextInput::make('rejected_qty')
                                    ->label('Rejected qty')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required(),
                                Select::make('lot_id')
                                    ->relationship('lot', 'lot_number')
                                    ->searchable()
                                    ->preload(),
                                Select::make('pallet_id')
                                    ->relationship('pallet', 'pallet_code')
                                    ->searchable()
                                    ->preload(),
                                Textarea::make('notes')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add received item')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(fn(array $state): string => filled($state['purchase_order_item_id'] ?? null)
                                ? 'PO item #' . $state['purchase_order_item_id']
                                : 'New received item')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
