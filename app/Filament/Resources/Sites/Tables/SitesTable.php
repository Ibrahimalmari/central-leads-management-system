<?php

namespace App\Filament\Resources\Sites\Tables;

use App\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label(__('admin.fields.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->limit(40)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab(),
                TextColumn::make('site_key')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('api_key_preview')
                    ->label(__('admin.fields.api_key'))
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('company_id')
                    ->label(__('admin.fields.company'))
                    ->relationship('company', 'name')
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
                Action::make('regenerateApiKey')
                    ->label(__('admin.actions.regenerate_api_key'))
                    ->requiresConfirmation()
                    ->action(function (Site $record): void {
                        $apiKey = Site::generateApiKey();

                        $record->update(['api_key' => $apiKey]);

                        Notification::make()
                            ->title(__('admin.actions.api_key_regenerated'))
                            ->body(__('admin.messages.api_key_visible_once', ['api_key' => $apiKey]))
                            ->success()
                            ->persistent()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
