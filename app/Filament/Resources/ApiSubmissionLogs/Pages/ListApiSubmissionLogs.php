<?php

namespace App\Filament\Resources\ApiSubmissionLogs\Pages;

use App\Filament\Resources\ApiSubmissionLogs\ApiSubmissionLogResource;
use Filament\Resources\Pages\ListRecords;

class ListApiSubmissionLogs extends ListRecords
{
    protected static string $resource = ApiSubmissionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
