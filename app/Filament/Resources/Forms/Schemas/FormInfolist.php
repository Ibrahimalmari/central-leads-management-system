<?php

namespace App\Filament\Resources\Forms\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FormInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('site.company.name')->label(__('admin.fields.company')),
                TextEntry::make('site.name')->label(__('admin.fields.site')),
                TextEntry::make('form_key'),
                TextEntry::make('name'),
                TextEntry::make('type')->placeholder('-'),
                TextEntry::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge(),
                TextEntry::make('leads_count')
                    ->label(__('admin.fields.leads'))
                    ->state(fn ($record) => $record->leads()->count()),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
