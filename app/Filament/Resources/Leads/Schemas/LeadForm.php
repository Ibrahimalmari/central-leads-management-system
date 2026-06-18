<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use App\Support\AccessControl;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
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
                Select::make('site_id')
                    ->label(__('admin.fields.site'))
                    ->options(fn () => AccessControl::scopeSites(Site::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('form_id')
                    ->label(__('admin.fields.known_form'))
                    ->options(fn () => AccessControl::scopeForms(Form::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                TextInput::make('form_key')->maxLength(255),
                TextInput::make('form_name')->maxLength(255),
                TextInput::make('form_type')->maxLength(255),
                TextInput::make('name')->maxLength(255),
                TextInput::make('email')->email()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(255),
                Textarea::make('message')->columnSpanFull(),
                TextInput::make('page_url')->url()->maxLength(2048)->columnSpanFull(),
                Select::make('status')
                    ->options(Lead::statusOptions())
                    ->default('new')
                    ->required(),
                Select::make('assigned_to')
                    ->label(__('admin.fields.assigned_to'))
                    ->options(fn () => AccessControl::scopeAssignableUsers(User::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                KeyValue::make('raw_data')
                    ->columnSpanFull(),
            ]);
    }
}
