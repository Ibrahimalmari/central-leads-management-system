<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use App\Support\AccessControl;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getTitle(): string
    {
        return __('filament-panels::pages/dashboard.title');
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.stats.dashboard_filters'))
                    ->description(__('admin.stats.dashboard_filters_description'))
                    ->extraAttributes(['class' => 'cl-dashboard-filters'])
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('admin.fields.from_date'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('admin.fields.until_date'))
                            ->native(false),
                        Select::make('company_id')
                            ->label(__('admin.fields.company'))
                            ->options(fn () => AccessControl::scopeCompanies(Company::query())->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => ! AccessControl::isAgent()),
                        Select::make('site_id')
                            ->label(__('admin.fields.site'))
                            ->options(fn () => AccessControl::scopeSites(Site::query())->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => ! AccessControl::isAgent()),
                        Select::make('form_id')
                            ->label(__('admin.fields.form'))
                            ->options(fn () => AccessControl::scopeForms(Form::query())->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => ! AccessControl::isAgent()),
                        Select::make('status')
                            ->label(__('admin.fields.status'))
                            ->options(Lead::statusOptions())
                            ->native(false),
                        Select::make('assigned_to')
                            ->label(__('admin.fields.assigned_to'))
                            ->options(fn () => AccessControl::scopeAssignableUsers(User::query())->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->visible(fn (): bool => ! AccessControl::isAgent()),
                    ])
                    ->columns([
                        'md' => 2,
                        'xl' => 4,
                    ])
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }
}
