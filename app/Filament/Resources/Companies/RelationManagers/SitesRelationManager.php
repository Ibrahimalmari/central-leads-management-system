<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\Site;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SitesRelationManager extends RelationManager
{
    protected static string $relationship = 'sites';

    private ?string $generatedApiKey = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->url()
                    ->required()
                    ->maxLength(255),
                TextInput::make('site_key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->default(fn () => 'site_'.Str::lower(Str::random(10))),
                Select::make('status')
                    ->options([
                        'active' => __('admin.statuses.active'),
                        'inactive' => __('admin.statuses.inactive'),
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('url')
                    ->limit(35)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab(),
                TextColumn::make('site_key')
                    ->copyable(),
                TextColumn::make('api_key_preview')
                    ->label(__('admin.fields.api_key'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge(),
                TextColumn::make('leads_count')
                    ->counts('leads')
                    ->label(__('admin.fields.leads')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => __('admin.statuses.active'),
                        'inactive' => __('admin.statuses.inactive'),
                    ]),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $this->generatedApiKey = Site::generateApiKey();
                        $data['api_key'] = $this->generatedApiKey;

                        return $data;
                    })
                    ->after(function (): void {
                        $this->sendApiKeyNotification(
                            __('admin.actions.api_key_created'),
                            (string) $this->generatedApiKey,
                        );

                        $this->generatedApiKey = null;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('regenerateApiKey')
                    ->label(__('admin.actions.regenerate_api_key'))
                    ->requiresConfirmation()
                    ->action(function (Site $record): void {
                        $apiKey = Site::generateApiKey();

                        $record->update(['api_key' => $apiKey]);

                        $this->sendApiKeyNotification(__('admin.actions.api_key_regenerated'), $apiKey);
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function sendApiKeyNotification(string $title, string $apiKey): void
    {
        Notification::make()
            ->title($title)
            ->body(__('admin.messages.api_key_visible_once', ['api_key' => $apiKey]))
            ->success()
            ->persistent()
            ->send();
    }
}
