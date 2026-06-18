<?php

namespace App\Filament\Widgets;

use App\Models\Form;
use App\Support\AccessControl;
use App\Support\DashboardLeadFilters;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TopForms extends TableWidget
{
    use InteractsWithPageFilters;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => AccessControl::scopeForms(Form::query())
                ->with('site.company')
                ->withCount([
                    'leads' => fn (Builder $query): Builder => DashboardLeadFilters::apply($query, $this->pageFilters),
                ])
                ->whereHas('leads', fn (Builder $query): Builder => DashboardLeadFilters::apply($query, $this->pageFilters))
                ->orderByDesc('leads_count'))
            ->columns([
                TextColumn::make('site.company.name')->label(__('admin.fields.company')),
                TextColumn::make('site.name')->label(__('admin.fields.site')),
                TextColumn::make('name')->label(__('admin.fields.form')),
                TextColumn::make('leads_count')->label(__('admin.fields.leads'))->sortable(),
            ]);
    }

    protected function getTableHeading(): string | Htmlable | null
    {
        return __('admin.stats.top_forms');
    }
}
