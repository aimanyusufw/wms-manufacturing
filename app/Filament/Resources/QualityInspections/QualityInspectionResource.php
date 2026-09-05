<?php

namespace App\Filament\Resources\QualityInspections;

use App\Filament\Resources\QualityInspections\Pages\CreateQualityInspection;
use App\Filament\Resources\QualityInspections\Pages\EditQualityInspection;
use App\Filament\Resources\QualityInspections\Pages\ListQualityInspections;
use App\Filament\Resources\QualityInspections\Schemas\QualityInspectionForm;
use App\Filament\Resources\QualityInspections\Tables\QualityInspectionsTable;
use App\Models\QualityInspection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Wezlo\FilamentApproval\RelationManagers\ApprovalsRelationManager;

class QualityInspectionResource extends Resource
{
    protected static ?string $model = QualityInspection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Quality Inspections';

    protected static ?string $modelLabel = 'Quality Inspection';

    protected static ?string $pluralModelLabel = 'Quality Inspections';

    protected static string|UnitEnum|null $navigationGroup = 'Inbound';

    protected static ?string $recordTitleAttribute = 'inspection_number';

    public static function form(Schema $schema): Schema
    {
        return QualityInspectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualityInspectionsTable::configure($table);
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
            'index' => ListQualityInspections::route('/'),
            'create' => CreateQualityInspection::route('/create'),
            'edit' => EditQualityInspection::route('/{record}/edit'),
        ];
    }
}
