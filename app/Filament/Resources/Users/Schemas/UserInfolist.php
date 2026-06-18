<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('role')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge(),
                TextEntry::make('company.name')->label(__('admin.fields.company'))->placeholder('-'),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
