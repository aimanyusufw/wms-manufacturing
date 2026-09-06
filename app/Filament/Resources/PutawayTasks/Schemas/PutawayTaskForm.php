<?php

namespace App\Filament\Resources\PutawayTasks\Schemas;

use App\Enums\DocumentStatus;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class PutawayTaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('What needs to be moved?')
                    ->description('Choose the received goods and quantity for this putaway task.')
                    ->schema([
                        Hidden::make('goods_receipt_id'),
                        Hidden::make('product_id'),
                        Hidden::make('lot_id'),
                        Hidden::make('pallet_id'),
                        Select::make('goods_receipt_item_id')
                            ->label('Received item')
                            ->options(fn(): array => GoodsReceiptItem::query()
                                ->with(['goodsReceipt', 'product', 'uom'])
                                ->whereHas('goodsReceipt', fn(Builder $query) => $query->whereIn('status', [
                                    DocumentStatus::APPROVED->value,
                                    DocumentStatus::COMPLETED->value,
                                ]))
                                ->get()
                                ->mapWithKeys(fn(GoodsReceiptItem $item): array => [
                                    $item->id => $item->goodsReceipt->document_number . ' | ' . $item->product->sku . ' - ' . $item->product->name . ' (' . $item->accepted_qty . ' ' . $item->uom->code . ')',
                                ])
                                ->all())
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?int $state, Set $set): void {
                                $item = $state ? GoodsReceiptItem::with(['goodsReceipt', 'product', 'lot', 'pallet'])->find($state) : null;
                                $set('goods_receipt_id', $item?->goods_receipt_id);
                                $set('product_id', $item?->product_id);
                                $set('lot_id', $item?->lot_id);
                                $set('pallet_id', $item?->pallet_id);
                                $set('qty', $item?->accepted_qty);
                            })
                            ->required(),
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
                            ->options(DocumentStatus::class)
                            ->default(DocumentStatus::DRAFT)
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
