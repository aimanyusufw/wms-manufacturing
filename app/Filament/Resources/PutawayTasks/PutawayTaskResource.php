<?php

namespace App\Filament\Resources\PutawayTasks;

use App\Filament\Resources\PutawayTasks\Pages\ManagePutawayTasks;
use App\Filament\Resources\PutawayTasks\Schemas\PutawayTaskForm;
use App\Filament\Resources\PutawayTasks\Tables\PutawayTasksTable;
use App\Models\PutawayTask;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PutawayTaskResource extends Resource
{
    protected static ?string $model = PutawayTask::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $navigationLabel = 'Putaway Tasks';

    protected static ?string $modelLabel = 'Putaway Task';

    protected static ?string $pluralModelLabel = 'Putaway Tasks';

    protected static string|UnitEnum|null $navigationGroup = 'Warehouse Operations';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PutawayTaskForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PutawayTasksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePutawayTasks::route('/'),
        ];
    }
}
