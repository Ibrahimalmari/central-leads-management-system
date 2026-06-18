<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Company;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'admin' => __('admin.statuses.admin'),
                        'manager' => __('admin.statuses.manager'),
                        'agent' => __('admin.statuses.agent'),
                    ])
                    ->default('agent')
                    ->required(),
                Select::make('company_id')
                    ->label(__('admin.fields.company'))
                    ->options(fn () => Company::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ]);
    }
}
