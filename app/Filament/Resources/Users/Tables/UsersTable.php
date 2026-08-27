<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('md')
                    ->slideOver(),
                DeleteAction::make(),
                Action::make('resetPassword')
                    ->label('Reset password')
                    ->authorize('update')
                    ->requiresConfirmation()
                    ->modalHeading('Reset user password')
                    ->form([
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->placeholder('Minimum 8 characters'),
                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn(string $operation, Get $get): bool => $operation === 'create' || filled($get('password')))
                            ->minLength(8)
                            ->placeholder('Re-enter your password')
                            ->helperText('Must match the password entered above.'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->update(['password' => $data['password']]);

                        Notification::make()
                            ->title('Password reset successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
