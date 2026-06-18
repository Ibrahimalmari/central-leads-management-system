<?php

namespace App\Filament\Resources\ApiSubmissionLogs\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ApiSubmissionLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.fields.date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'success' ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('http_status')
                    ->label(__('admin.fields.http_status'))
                    ->sortable(),
                TextColumn::make('site.name')
                    ->label(__('admin.fields.site'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('lead_id')
                    ->label(__('admin.fields.lead_id'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('form_key')
                    ->label(__('admin.fields.form_key'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('message')
                    ->label(__('admin.fields.message'))
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label(__('admin.fields.ip_address'))
                    ->copyable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'success' => __('admin.statuses.success'),
                        'failed' => __('admin.statuses.failed'),
                    ]),
            ]);
    }
}
