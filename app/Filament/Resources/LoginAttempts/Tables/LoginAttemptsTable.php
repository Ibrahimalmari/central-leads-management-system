<?php

namespace App\Filament\Resources\LoginAttempts\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LoginAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('attempted_at')
                    ->label(__('admin.fields.date'))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('successful')
                    ->label(__('admin.fields.successful'))
                    ->boolean(),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('user.name')
                    ->label(__('admin.fields.user'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label(__('admin.fields.ip_address'))
                    ->copyable()
                    ->searchable(),
                TextColumn::make('user_agent')
                    ->label(__('admin.fields.user_agent'))
                    ->limit(55),
            ])
            ->filters([
                TernaryFilter::make('successful')
                    ->label(__('admin.fields.successful')),
            ]);
    }
}
