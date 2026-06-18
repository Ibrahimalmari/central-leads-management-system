<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use App\Support\AccessControl;
use App\Support\LeadCsvExport;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.fields.date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('admin.fields.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('site.name')
                    ->label(__('admin.fields.site'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('form.name')
                    ->label(__('admin.fields.form'))
                    ->placeholder(fn ($record) => $record->form_name ?: $record->form_key ?: '-')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('email')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => __("admin.statuses.{$state}"))
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label(__('admin.fields.assigned_to'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('page_url')
                    ->label(__('admin.fields.page'))
                    ->limit(35)
                    ->url(fn ($state) => $state)
                    ->openUrlInNewTab()
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label(__('admin.fields.company'))
                    ->options(fn () => AccessControl::scopeCompanies(Company::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('site_id')
                    ->label(__('admin.fields.site'))
                    ->options(fn () => AccessControl::scopeSites(Site::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('form_id')
                    ->label(__('admin.fields.form'))
                    ->options(fn () => AccessControl::scopeForms(Form::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('status')
                    ->options(Lead::statusOptions()),
                SelectFilter::make('assigned_to')
                    ->label(__('admin.fields.assigned_to'))
                    ->options(fn () => AccessControl::scopeAssignableUsers(User::query())->orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))),
            ])
            ->headerActions([
                Action::make('exportLeads')
                    ->label(__('admin.actions.export_leads'))
                    ->icon(Heroicon::ArrowDownTray)
                    ->action(fn (HasTable $livewire) => app(LeadCsvExport::class)->downloadFromQuery(
                        $livewire->getTableQueryForExport(),
                    )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportSelectedLeads')
                        ->label(__('admin.actions.export_selected_leads'))
                        ->icon(Heroicon::ArrowDownTray)
                        ->action(fn (Collection $records) => app(LeadCsvExport::class)->downloadFromRecords(
                            $records,
                        ))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
