<?php

namespace App\Filament\Resources\Sites\Schemas;

use App\Models\Company;
use App\Support\AccessControl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label(__('admin.fields.company'))
                    ->options(fn () => AccessControl::scopeCompanies(Company::query())->orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => AccessControl::isManager() ? AccessControl::user()?->company_id : null)
                    ->disabled(fn (): bool => AccessControl::isManager())
                    ->dehydrated()
                    ->searchable()
                    ->required(),
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
                    ->maxLength(255)
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
}
