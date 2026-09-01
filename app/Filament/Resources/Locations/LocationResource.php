<?php

namespace App\Filament\Resources\Locations;

use App\Enums\LocationType;
use App\Filament\Resources\Locations\Pages\ManageLocations;
use App\Models\Location;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class LocationResource extends Resource
{
    protected static ?string $model = Location::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('warehouse_id')
                ->label('Warehouse')
                ->relationship(
                    name: 'warehouse',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn(Builder $query) =>
                    $query->where('is_active', true)
                )
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(
                    fn(callable $set) => $set('parent_id', null)
                )
                ->placeholder('Select a warehouse'),

            Select::make('location_type')
                ->label('Location Type')
                ->options(LocationType::class)
                ->required()
                ->live()
                ->placeholder('Select location type'),

            Select::make('parent_id')
                ->label('Parent Location')
                ->relationship(
                    name: 'parent',
                    titleAttribute: 'code',
                    modifyQueryUsing: function (
                        Builder $query,
                        Get $get,
                        ?Location $record
                    ) {
                        $warehouseId = $get('warehouse_id');

                        $query
                            ->whereNull('deleted_at')
                            ->when(
                                $warehouseId,
                                fn(Builder $query) =>
                                $query->where(
                                    'warehouse_id',
                                    $warehouseId
                                )
                            )
                            ->when(
                                $record,
                                fn(Builder $query) =>
                                $query->where(
                                    'id',
                                    '!=',
                                    $record->id
                                )
                            )
                            ->orderBy('code');
                    }
                )
                ->getOptionLabelFromRecordUsing(
                    function (Location $record): string {
                        return (string) (
                            $record->full_path
                            ?: $record->code
                            ?: $record->name
                            ?: "Location #{$record->id}"
                        );
                    }
                )
                ->searchable()
                ->preload()
                ->live()
                ->placeholder('Select a parent location'),

            TextInput::make('code')
                ->label('Location Code')
                ->unique()
                ->placeholder('e.g. RM-A-R01-B01')
                ->required()
                ->maxLength(100),

            TextInput::make('name')
                ->label('Location Name')
                ->placeholder('e.g. Raw Material Rack A01 Bin 01')
                ->required()
                ->maxLength(255),

            TextInput::make('max_capacity')
                ->label('Maximum Capacity')
                ->numeric()
                ->placeholder('e.g. 1000'),

            Select::make('capacity_uom')
                ->label('Capacity UOM')
                ->searchable()
                ->preload()
                ->relationship("uom", "code"),

            Textarea::make('description')
                ->label('Description')
                ->placeholder('Enter optional location details or notes...')
                ->rows(3)
                ->columnSpanFull(),

            Toggle::make('is_pickable')
                ->label('Can Be Used for Picking')
                ->default(false),

            Toggle::make('is_putaway_allowed')
                ->label('Allow Putaway')
                ->default(false),

            Toggle::make('is_active')
                ->label('Active Location')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Location Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('parent.code')
                    ->label('Parent Location')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('level')
                    ->label('Level')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_pickable')
                    ->label('Pickable')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_putaway_allowed')
                    ->label('Putaway')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('location_type')
                    ->label('Location Type')
                    ->options(LocationType::class),

                TernaryFilter::make('is_pickable')
                    ->label('Pickable'),

                TernaryFilter::make('is_putaway_allowed')
                    ->label('Putaway Allowed'),

                TernaryFilter::make('is_active')
                    ->label('Status'),

                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('warehouse_id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLocations::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
