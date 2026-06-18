<?php

namespace App\Filament\Resources\Forms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FormsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site.company.name')
                    ->label(__('admin.fields.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('site.name')
                    ->label(__('admin.fields.site'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('form_key')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge()
                    ->sortable(),
                TextColumn::make('leads_count')
                    ->counts('leads')
                    ->label(__('admin.fields.leads'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('site_id')
                    ->label(__('admin.fields.site'))
                    ->relationship('site', 'name')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options([
                        'active' => __('admin.statuses.active'),
                        'inactive' => __('admin.statuses.inactive'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
