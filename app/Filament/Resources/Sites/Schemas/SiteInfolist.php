<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('company.name')->label(__('admin.fields.company')),
                TextEntry::make('name'),
                TextEntry::make('url')->url(fn ($state) => $state)->openUrlInNewTab(),
                TextEntry::make('site_key'),
                TextEntry::make('api_key_preview')
                    ->label(__('admin.fields.api_key'))
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge(),
                TextEntry::make('forms_count')
                    ->label(__('admin.fields.forms'))
                    ->state(fn ($record) => $record->forms()->count()),
                TextEntry::make('leads_count')
                    ->label(__('admin.fields.leads'))
                    ->state(fn ($record) => $record->leads()->count()),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
