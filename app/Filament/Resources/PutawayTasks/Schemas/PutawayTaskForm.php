<?php

namespace App\Filament\Resources\PutawayTasks\Schemas;

use App\Enums\PutawayStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PutawayTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Inbound source')
                    ->description('Generic putaway can be created from a Goods Receipt or a Production Receipt.')
                    ->schema([
                        Select::make('source_type')
                            ->label('Inbound source type')
                            ->options([
                                GoodsReceipt::class => 'Goods Receipt (GRN)',
                                ProductionReceipt::class => 'Production Receipt',
                            ])
                            ->default(GoodsReceipt::class)
                            ->searchable()
                            ->live()
                            ->required(),
                        Select::make('goods_receipt_id')
                            ->label('GRN document')
                            ->options(fn(): array => GoodsReceipt::query()
                                ->orderBy('document_number')
                                ->pluck('document_number', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->visible(fn(Get $get): bool => $get('source_type') === GoodsReceipt::class)
                            ->nullable(),
                        Select::make('production_receipt_id')
                            ->label('Production receipt document')
                            ->options(fn(): array => ProductionReceipt::query()
                                ->orderBy('document_number')
                                ->pluck('document_number', 'id')
                                ->toArray())
                            ->searchable()
                            ->preload()
                            ->visible(fn(Get $get): bool => $get('source_type') === ProductionReceipt::class)
                            ->nullable(),
                    ]),
                Section::make('What needs to be moved?')
                    ->description('Choose the received inventory reference, source staging bin, and destination storage bin.')
                    ->schema([
                        Hidden::make('product_id'),
                        Hidden::make('lot_id'),
                        Hidden::make('pallet_id'),
                        Select::make('goods_receipt_item_id')
                            ->label('GRN received item')
                            ->options(fn(): array => GoodsReceiptItem::query()
                                ->with(['goodsReceipt', 'product', 'uom'])
                                ->whereHas('goodsReceipt', fn(Builder $query) => $query->whereIn('status', ['approved', 'completed']))
                                ->get()
                                ->mapWithKeys(fn(GoodsReceiptItem $item): array => [
                                    $item->id => $item->goodsReceipt->document_number . ' | ' . $item->product->sku . ' - ' . $item->product->name . ' (' . $item->accepted_qty . ' ' . $item->uom->code . ')',
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->visible(fn(Get $get): bool => $get('source_type') === GoodsReceipt::class)
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $item = $state ? GoodsReceiptItem::with(['goodsReceipt', 'product', 'lot', 'pallet'])->find($state) : null;
                                $set('goods_receipt_id', $item?->goods_receipt_id);
                                $set('product_id', $item?->product_id);
                                $set('lot_id', $item?->lot_id);
                                $set('pallet_id', $item?->pallet_id);
                                $set('qty', $item?->accepted_qty);
                            })
                            ->nullable(),
                        Select::make('production_receipt_item_id')
                            ->label('Production receipt item')
                            ->options(fn(): array => ProductionReceiptItem::query()
                                ->with(['productionReceipt', 'product', 'uom'])
                                ->whereHas('productionReceipt', fn(Builder $query) => $query->whereIn('status', ['approved', 'completed']))
                                ->get()
                                ->mapWithKeys(fn(ProductionReceiptItem $item): array => [
                                    $item->id => $item->productionReceipt->document_number . ' | ' . $item->product->sku . ' - ' . $item->product->name . ' (' . $item->received_qty . ' ' . $item->uom->code . ')',
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->visible(fn(Get $get): bool => $get('source_type') === ProductionReceipt::class)
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $item = $state ? ProductionReceiptItem::with(['productionReceipt', 'product', 'lot', 'pallet'])->find($state) : null;
                                $set('production_receipt_id', $item?->production_receipt_id);
                                $set('product_id', $item?->product_id);
                                $set('lot_id', $item?->lot_id);
                                $set('pallet_id', $item?->pallet_id);
                                $set('qty', $item?->received_qty);
                            })
                            ->nullable(),
                        Select::make('destination_bin_id')
                            ->label('Destination bin')
                            ->options(fn(): array => Location::query()
                                ->with('warehouse')
                                ->where('is_active', true)
                                ->where('is_putaway_allowed', true)
                                ->orderBy('warehouse_id')
                                ->orderBy('code')
                                ->get()
                                ->mapWithKeys(fn(Location $location): array => [
                                    $location->id => $location->warehouse->name . ' / ' . $location->code . ' - ' . $location->name,
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('source_bin_id')
                            ->label('Source bin')
                            ->options(fn(): array => Location::query()
                                ->with('warehouse')
                                ->where('is_active', true)
                                ->orderBy('warehouse_id')
                                ->orderBy('code')
                                ->get()
                                ->mapWithKeys(fn(Location $location): array => [
                                    $location->id => $location->warehouse->name . ' / ' . $location->code . ' - ' . $location->name,
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('qty')
                            ->label('Quantity to put away')
                            ->numeric()
                            ->minValue(0.000001)
                            ->required(),
                    ]),
                Section::make('Task ownership')
                    ->description('Assign the task and track its progress.')
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Assigned to')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Select::make('status')
                            ->options(PutawayStatus::class)
                            ->default(PutawayStatus::PENDING)
                            ->required(),
                        DateTimePicker::make('completed_at')
                            ->label('Completed at')
                            ->native(false)
                            ->disabled()
                            ->dehydrated(),
                    ])
            ])->columns(1);
    }
}
