<?php

namespace App\Filament\Resources\PutawayTasks\Tables;

use App\Enums\DocumentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PutawayTasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('goodsReceipt.document_number')
                    ->label('GRN')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label('Product')
                    ->description(fn($record): ?string => $record->product?->sku)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sourceBin.code')
                    ->label('From')
                    ->placeholder('Receiving area')
                    ->description(fn($record): ?string => $record->sourceBin?->name),
                TextColumn::make('destinationBin.code')
                    ->label('To')
                    ->description(fn($record): ?string => $record->destinationBin?->name)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(decimalPlaces: 3)
                    ->suffix(fn($record): ?string => $record->goodsReceiptItem?->uom?->code),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned to')
                    ->placeholder('Unassigned')
                    ->searchable(),
                TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(DocumentStatus::class),
                SelectFilter::make('assigned_to')
                    ->label('Assignee')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
            ])
            ->toolbarActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
