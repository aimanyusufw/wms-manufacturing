<?php

namespace App\Filament\Resources\Customers;

use App\Filament\Resources\Customers\Pages\ManageCustomers;
use App\Models\Customer;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Parfaitementweb\FilamentCountryField\Forms\Components\Country;
use Parfaitementweb\FilamentCountryField\Tables\Columns\CountryColumn;
use UnitEnum;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Supplier Code')
                    ->placeholder('SUP-RW-IDN-001')
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->required(),

                TextInput::make('name')
                    ->label('Company Name')
                    ->placeholder('e.g. PT Good Company')
                    ->maxLength(255)
                    ->required(),

                TextInput::make('contact_person')
                    ->label('Contact Person')
                    ->placeholder('e.g. John Doe')
                    ->maxLength(255),

                PhoneInput::make('phone')
                    ->label('Phone Number')
                    ->placeholder('+62 812-3456-7890'),

                TextInput::make('email')
                    ->label('Email Address')
                    ->placeholder('contact@company.com')
                    ->email()
                    ->maxLength(255),

                TextInput::make('tax_number')
                    ->label('Tax Identification Number (NPWP)')
                    ->placeholder('00.000.000.0-000.000')
                    ->maxLength(50),

                Toggle::make('is_active')
                    ->label('Active Status')
                    ->default(true)
                    ->required(),

                Country::make('country')
                    ->searchable(),

                Textarea::make('address')
                    ->label('Full Address')
                    ->placeholder('Enter full company address...')
                    ->rows(4)
                    ->maxLength(500)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                CountryColumn::make('country')
                    ->placeholder("null"),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable()
                    ->placeholder("null")
                    ->toggleable(),

                PhoneColumn::make('phone')
                    ->placeholder("phone is empty")
                    ->copyable()
                    ->displayFormat(PhoneInputNumberType::NATIONAL),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->placeholder("null")
                    ->url(fn($record) => $record->email ? "mailto:{$record->email}" : null)
                    ->toggleable(),

                TextColumn::make('tax_number')
                    ->label('Tax ID (NPWP)')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => ManageCustomers::route('/'),
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
