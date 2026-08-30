<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductType;
use App\Filament\Resources\Products\Pages\ManageProducts;
use App\Models\Product;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGift;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->placeholder('e.g. Wireless Ergonomic Mouse')
                    ->columnSpan(2),

                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select a category'),

                Select::make('base_uom_id')
                    ->relationship('baseUom', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->placeholder('Select unit of measure'),

                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->placeholder('e.g. ELEC-MSE-001')
                    ->helperText('Stock Keeping Unit (unique identifier).'),

                TextInput::make('barcode')
                    ->placeholder('e.g. 8991234567890')
                    ->helperText('EAN, UPC, or custom barcode string.'),

                Select::make('product_type')
                    ->required()
                    ->options(ProductType::class)
                    ->helperText('Classification type of the product.')
                    ->required(),

                Textarea::make('description')
                    ->placeholder('Enter detailed product specifications or description...')
                    ->columnSpanFull(),

                TextInput::make('min_stock')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->placeholder('0.00')
                    ->helperText('Minimum inventory balance before alert.'),

                TextInput::make('max_stock')
                    ->numeric()
                    ->placeholder('0.00')
                    ->helperText('Maximum stock threshold allowed in warehouse.'),

                TextInput::make('reorder_point')
                    ->numeric()
                    ->placeholder('0.00')
                    ->helperText('Stock level that triggers a replenishment order.'),

                TextInput::make('shelf_life_days')
                    ->numeric()
                    ->placeholder('e.g. 365')
                    ->helperText('Product expiration timeframe in days.'),

                Toggle::make('track_lot')
                    ->label('Track Lot Number')
                    ->helperText('Enable lot batch tracking for this product.'),

                Toggle::make('track_serial')
                    ->label('Track Serial Number')
                    ->helperText('Enable individual serial number tracking.'),

                Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true)
                    ->helperText('Deactivating hides this product from active catalog.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('product_type')
                    ->sortable()
                    ->badge(),

                TextColumn::make('baseUom.name')
                    ->label('UOM')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('barcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('min_stock')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('max_stock')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reorder_point')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('shelf_life_days')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('track_lot')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('track_serial')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProducts::route('/'),
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
