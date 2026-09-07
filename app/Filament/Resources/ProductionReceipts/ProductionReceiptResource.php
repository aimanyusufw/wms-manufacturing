<?php

namespace App\Filament\Resources\ProductionReceipts;

use App\Filament\Resources\ProductionReceipts\Pages\CreateProductionReceipt;
use App\Filament\Resources\ProductionReceipts\Pages\EditProductionReceipt;
use App\Filament\Resources\ProductionReceipts\Pages\ListProductionReceipts;
use App\Filament\Resources\ProductionReceipts\Schemas\ProductionReceiptForm;
use App\Filament\Resources\ProductionReceipts\Tables\ProductionReceiptsTable;
use App\Models\ProductionReceipt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Wezlo\FilamentApproval\RelationManagers\ApprovalsRelationManager;

class ProductionReceiptResource extends Resource
{
    protected static ?string $model = ProductionReceipt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Production Receipts';

    protected static ?string $modelLabel = 'Production Receipt';

    protected static ?string $pluralModelLabel = 'Production Receipts';

    protected static string|UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?string $recordTitleAttribute = 'document_number';

    public static function form(Schema $schema): Schema
    {
        return ProductionReceiptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionReceiptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ApprovalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductionReceipts::route('/'),
            'create' => CreateProductionReceipt::route('/create'),
            'edit' => EditProductionReceipt::route('/{record}/edit'),
        ];
    }
}
