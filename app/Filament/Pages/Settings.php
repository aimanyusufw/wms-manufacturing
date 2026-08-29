<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

use UnitEnum;

class Settings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermissionTo('View:Settings') ?? false;
    }

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('Company Profile')
                            ->icon(Heroicon::OutlinedBuildingOffice2)
                            ->schema([
                                Section::make('Company Information')
                                    ->description('Manage primary business profile and contact details.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('company_name')
                                                ->label('Company Name')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('e.g. Acme Manufacturing Ltd.'),

                                            TextInput::make('company_email')
                                                ->label('Company Email')
                                                ->email()
                                                ->required()
                                                ->placeholder('contact@acme.com'),

                                            TextInput::make('company_phone')
                                                ->label('Phone Number')
                                                ->tel()
                                                ->maxLength(50)
                                                ->placeholder('+1 (555) 000-0000'),

                                            TextInput::make('company_website')
                                                ->label('Website URL')
                                                ->url()
                                                ->placeholder('https://example.com'),

                                            TextInput::make('tax_id')
                                                ->label('Tax ID / VAT Number')
                                                ->maxLength(50)
                                                ->placeholder('e.g. US-123456789'),
                                        ]),

                                        TextInput::make('company_address')
                                            ->label('Official Business Address')
                                            ->required()
                                            ->maxLength(500)
                                            ->placeholder('Street, Building, City, State, ZIP'),

                                        CuratorPicker::make('company_logo_id')
                                            ->label('Company Logo')
                                            ->buttonLabel('Choose Company Logo')
                                            ->color('primary')
                                            ->outlined()
                                            ->size('sm'),
                                    ]),
                            ]),

                        Tab::make('Localization')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema([
                                Section::make('Regional & Currency Settings')
                                    ->description('Configure display formats and currency units.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('currency_code')
                                                ->label('Currency Code')
                                                ->options([
                                                    'USD' => 'USD - US Dollar ($)',
                                                    'EUR' => 'EUR - Euro (€)',
                                                    'GBP' => 'GBP - British Pound (£)',
                                                    'IDR' => 'IDR - Indonesian Rupiah (Rp)',
                                                    'JPY' => 'JPY - Japanese Yen (¥)',
                                                    'SGD' => 'SGD - Singapore Dollar ($)',
                                                ])
                                                ->searchable()
                                                ->required(),

                                            TextInput::make('currency_symbol')
                                                ->label('Currency Symbol')
                                                ->required()
                                                ->maxLength(10)
                                                ->placeholder('$'),

                                            Select::make('timezone')
                                                ->label('Timezone')
                                                ->options([
                                                    'UTC' => 'UTC (Coordinated Universal Time)',
                                                    'Asia/Jakarta' => 'Asia/Jakarta (WIB, UTC+7)',
                                                    'Asia/Makassar' => 'Asia/Makassar (WITA, UTC+8)',
                                                    'Asia/Jayapura' => 'Asia/Jayapura (WIT, UTC+9)',
                                                    'Asia/Singapore' => 'Asia/Singapore (UTC+8)',
                                                    'America/New_York' => 'America/New_York (EST/EDT)',
                                                    'America/Los_Angeles' => 'America/Los_Angeles (PST/PDT)',
                                                    'Europe/London' => 'Europe/London (GMT/BST)',
                                                ])
                                                ->searchable()
                                                ->required(),

                                            Select::make('date_format')
                                                ->label('Date Format')
                                                ->options([
                                                    'Y-m-d' => 'YYYY-MM-DD (2026-08-25)',
                                                    'd/m/Y' => 'DD/MM/YYYY (25/08/2026)',
                                                    'm/d/Y' => 'MM/DD/YYYY (08/25/2026)',
                                                    'd M Y' => 'DD MMM YYYY (25 Aug 2026)',
                                                ])
                                                ->required(),

                                            Select::make('time_format')
                                                ->label('Time Format')
                                                ->options([
                                                    'H:i:s' => '24-hour with seconds (14:30:00)',
                                                    'H:i' => '24-hour without seconds (14:30)',
                                                    'h:i A' => '12-hour AM/PM (02:30 PM)',
                                                ])
                                                ->required(),
                                        ]),
                                    ]),
                            ]),

                        Tab::make('WMS & Inventory')
                            ->icon(Heroicon::OutlinedCube)
                            ->schema([
                                Section::make('Inventory & Measurement Defaults')
                                    ->description('Define default SKU structure, stock alerts, and measurement units.')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Select::make('default_weight_unit')
                                                ->label('Default Weight Unit')
                                                ->options([
                                                    'kg' => 'Kilogram (kg)',
                                                    'g' => 'Gram (g)',
                                                    'lbs' => 'Pound (lbs)',
                                                    'ton' => 'Metric Ton (t)',
                                                ])
                                                ->required(),

                                            Select::make('default_dimension_unit')
                                                ->label('Default Dimension Unit')
                                                ->options([
                                                    'cm' => 'Centimeter (cm)',
                                                    'm' => 'Meter (m)',
                                                    'mm' => 'Millimeter (mm)',
                                                    'in' => 'Inch (in)',
                                                ])
                                                ->required(),

                                            TextInput::make('sku_prefix')
                                                ->label('SKU Default Prefix')
                                                ->maxLength(20)
                                                ->placeholder('WMS-'),

                                            TextInput::make('low_stock_threshold')
                                                ->label('Global Low Stock Warning Threshold')
                                                ->numeric()
                                                ->minValue(0)
                                                ->required()
                                                ->helperText('Trigger alerts when item stock falls below this quantity.'),
                                        ]),

                                        Toggle::make('enable_auto_sku_generation')
                                            ->label('Enable Automatic SKU Generation')
                                            ->helperText('Automatically generate unique SKU codes for new manufactured products and raw materials.')
                                            ->default(true),
                                    ]),
                            ]),

                        Tab::make('System & Audit')
                            ->icon(Heroicon::OutlinedShieldCheck)
                            ->schema([
                                Section::make('System Logs & Audit Preferences')
                                    ->description('Configure security and system audit options.')
                                    ->schema([
                                        Toggle::make('enable_activity_logging')
                                            ->label('Enable Activity Trail Logging')
                                            ->helperText('Record warehouse movements, stock adjustments, and configuration changes into the audit log.')
                                            ->default(true),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
