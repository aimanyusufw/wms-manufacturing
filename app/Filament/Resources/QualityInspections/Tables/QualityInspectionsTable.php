<?php

namespace App\Filament\Resources\QualityInspections\Tables;

use App\Enums\QcStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Wezlo\FilamentApproval\Columns\ApprovalStatusColumn;

class QualityInspectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('inspection_number')
                    ->label('Inspection number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('goodsReceipt.document_number')
                    ->label('Goods receipt')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('productionReceipt.id')
                    ->label('Production receipt')
                    ->placeholder('-'),
                TextColumn::make('inspection_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('QC status')
                    ->badge(),
                ApprovalStatusColumn::make(),
                TextColumn::make('inspector.name')
                    ->label('Inspected by')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approver.name')
                    ->label('Approved by')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(QcStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
