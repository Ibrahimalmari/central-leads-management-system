<?php

namespace App\Filament\Resources\SystemSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label(__('admin.fields.system_logo'))
                    ->disk('public')
                    ->height(40)
                    ->defaultImageUrl(asset('images/central-leads-logo.svg')),
                TextColumn::make('app_name')
                    ->label(__('admin.fields.system_name'))
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label(__('admin.fields.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
