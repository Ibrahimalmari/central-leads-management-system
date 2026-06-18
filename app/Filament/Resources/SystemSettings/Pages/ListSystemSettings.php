<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use App\Models\SystemSetting;
use Filament\Resources\Pages\ListRecords;

class ListSystemSettings extends ListRecords
{
    protected static string $resource = SystemSettingResource::class;

    public function mount(): void
    {
        SystemSetting::current();

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
