<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('John Doe')
                    ->helperText('Enter the user\'s full name.'),
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('user@company.com')
                    ->helperText('Use an active email address that is not taken by another user.'),
                TextInput::make('password')
                    ->password()
                    ->visible(fn(string $operation): bool => $operation === 'create')
                    ->revealable()
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->placeholder('Minimum 8 characters'),
                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->visible(fn(string $operation): bool => $operation === 'create')
                    ->password()
                    ->revealable()
                    ->same('password')
                    ->dehydrated(false)
                    ->required(fn(string $operation, Get $get): bool => $operation === 'create' || filled($get('password')))
                    ->minLength(8)
                    ->placeholder('Re-enter your password')
                    ->helperText('Must match the password entered above.'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                Section::make('Audit information')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created')
                            ->content(fn(?User $record): string => $record?->created_at?->diffForHumans() ?? '-'),
                        Placeholder::make('updated_at')
                            ->label('Last updated')
                            ->content(fn(?User $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                    ])
                    ->visible(fn(string $operation): bool => $operation === 'edit')->columnSpanFull(),
            ]);
    }
}
