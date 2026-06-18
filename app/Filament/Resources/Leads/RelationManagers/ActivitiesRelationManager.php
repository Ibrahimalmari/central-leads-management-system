<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.fields.date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->formatStateUsing(fn (string $state): string => __("admin.activities.types.{$state}"))
                    ->badge(),
                TextColumn::make('title')
                    ->label(__('admin.fields.description'))
                    ->wrap()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label(__('admin.fields.user'))
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
