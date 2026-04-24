<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([

                        TextInput::make('name')
                            ->label(__('User Name'))
                            ->required()
                            ->maxLength(25),

                        TextInput::make('email')
                            ->label(__('Email Address'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Radio::make('is_admin')
                            ->label(__('Type'))
                            ->required()
                            ->boolean(trueLabel: __('Administrator'), falseLabel: __('User')),

                        TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrateStateUsing(fn ($state) => filled($state)
                                ? bcrypt(is_string($state) ? $state : '')
                                : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->maxLength(20),
                    ]),
            ]);
    }
}
