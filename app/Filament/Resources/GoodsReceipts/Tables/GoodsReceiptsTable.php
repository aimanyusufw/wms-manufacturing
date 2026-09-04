<?php

namespace App\Filament\Resources\GoodsReceipts\Tables;

use App\Enums\DocumentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wezlo\FilamentApproval\Columns\ApprovalStatusColumn;

class GoodsReceiptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('GRN number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('purchaseOrder.document_number')
                    ->label('Purchase order')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('receipt_date')
                    ->label('Receipt date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('GRN status')
                    ->badge(),
                ApprovalStatusColumn::make(),
                TextColumn::make('receiver.name')
                    ->label('Received by')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('delivery_note_number')
                    ->label('Delivery note')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(DocumentStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
