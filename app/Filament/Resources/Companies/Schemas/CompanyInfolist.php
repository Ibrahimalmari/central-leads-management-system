<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->placeholder('-'),
                TextEntry::make('phone')->placeholder('-'),
                TextEntry::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge(),
                TextEntry::make('sites_count')
                    ->label(__('admin.fields.sites'))
                    ->state(fn ($record) => $record->sites()->count()),
                TextEntry::make('leads_count')
                    ->label(__('admin.fields.leads'))
                    ->state(fn ($record) => $record->leads()->count()),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
