<?php

namespace App\Filament\Resources\PutawayTasks\Tables;

use App\Enums\PutawayStatus;
use App\Models\GoodsReceipt;
use App\Models\Location;
use App\Models\ProductionReceipt;
use App\Models\PutawayTask;
use App\Services\PutawayExecutionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PutawayTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inbound_source')
                    ->label('Inbound Source')
                    ->state(function ($record): string {
                        if ($record->source_type === GoodsReceipt::class && $record->goodsReceipt) {
                            return 'GRN: ' . $record->goodsReceipt->document_number;
                        }

                        if ($record->source_type === ProductionReceipt::class && $record->productionReceipt) {
                            return 'PR: ' . $record->productionReceipt->document_number;
                        }

                        return $record->source_type ? class_basename($record->source_type) : 'Unknown';
                    })
                    ->description(fn($record): ?string => $record->source_type ? class_basename($record->source_type) : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->description(fn($record): ?string => $record->product?->sku)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.lot_number')
                    ->label('Lot')
                    ->placeholder('No lot')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('pallet.pallet_code')
                    ->label('Pallet')
                    ->placeholder('No pallet')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sourceBin.code')
                    ->label('Source Bin')
                    ->placeholder('Staging')
                    ->description(fn($record): ?string => $record->sourceBin?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('destinationBin.code')
                    ->label('Dest. Bin')
                    ->description(fn($record): ?string => $record->destinationBin?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(decimalPlaces: 3)
                    ->suffix(fn($record): ?string => $record->product?->baseUom?->code ?? ''),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned to')
                    ->placeholder('Unassigned')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PutawayStatus::class),
                SelectFilter::make('assigned_to')
                    ->label('Assignee')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('source_type')
                    ->label('Inbound Source')
                    ->options([
                        GoodsReceipt::class => 'GRN',
                        ProductionReceipt::class => 'Production Receipt',
                    ]),
            ])
            ->recordActions([
                Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn(PutawayTask $record): bool => $record->status === PutawayStatus::PENDING)
                    ->form([
                        FormSelect::make('assigned_to')
                            ->label('Assigned to')
                            ->relationship('assignee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->modalHeading('Assign Putaway Task')
                    ->modalSubmitActionLabel('Assign')
                    ->action(function (PutawayTask $record, array $data): void {
                        app(PutawayExecutionService::class)->assignTask($record, (int) $data['assigned_to']);
                    }),
                Action::make('start')
                    ->label('Start')
                    ->icon('heroicon-o-play')
                    ->visible(fn(PutawayTask $record): bool => $record->status === PutawayStatus::ASSIGNED)
                    ->requiresConfirmation()
                    ->action(function (PutawayTask $record): void {
                        app(PutawayExecutionService::class)->startTask($record);
                    }),
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check')
                    ->visible(fn(PutawayTask $record): bool => $record->status === PutawayStatus::IN_PROGRESS)
                    ->form([
                        FormSelect::make('destination_bin_id')
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
                    ])
                    ->modalHeading('Complete Putaway Task')
                    ->modalSubmitActionLabel('Complete task')
                    ->action(function (PutawayTask $record, array $data): void {
                        app(PutawayExecutionService::class)->completeTask($record, (int) $data['destination_bin_id'], Auth::id());
                    }),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
