<?php

namespace App\Filament\Resources\Sites\Pages;

use App\Filament\Resources\Sites\SiteResource;
use App\Models\Site;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    private ?string $generatedApiKey = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->generatedApiKey = Site::generateApiKey();
        $data['api_key'] = $this->generatedApiKey;

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title(__('admin.actions.api_key_created'))
            ->body(__('admin.messages.api_key_visible_once', ['api_key' => $this->generatedApiKey]))
            ->success()
            ->persistent()
            ->send();

        $this->generatedApiKey = null;
    }
}
