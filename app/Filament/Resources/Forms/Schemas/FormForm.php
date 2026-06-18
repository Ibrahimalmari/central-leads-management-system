<?php

namespace App\Filament\Resources\Forms\Schemas;

use App\Models\Site;
use App\Support\AccessControl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class FormForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('site_id')
                    ->label(__('admin.fields.site'))
                    ->options(fn () => AccessControl::scopeSites(Site::query())->with('company')->orderBy('name')->get()->mapWithKeys(fn ($site) => [
                        $site->id => "{$site->company->name} / {$site->name}",
                    ]))
                    ->searchable()
                    ->required(),
                TextInput::make('form_key')
                    ->required()
                    ->maxLength(255)
                    ->rules(fn ($record, $get) => [
                        Rule::unique('forms', 'form_key')
                            ->where('site_id', $get('site_id'))
                            ->ignore($record?->id),
                    ]),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('type')
                    ->maxLength(255),
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
