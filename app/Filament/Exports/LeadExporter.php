<?php

namespace App\Filament\Exports;

use App\Models\Lead;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class LeadExporter extends Exporter
{
    protected static ?string $model = Lead::class;

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('company.name'),
            ExportColumn::make('site.name'),
            ExportColumn::make('form.name'),
            ExportColumn::make('form_key'),
            ExportColumn::make('form_name'),
            ExportColumn::make('form_type'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('message'),
            ExportColumn::make('page_url'),
            ExportColumn::make('status'),
            ExportColumn::make('assignee.name'),
            ExportColumn::make('assigned_at'),
            ExportColumn::make('last_contacted_at'),
            ExportColumn::make('raw_data'),
            ExportColumn::make('ip_address'),
            ExportColumn::make('user_agent'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your lead export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
