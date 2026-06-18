<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['assigned_to'])) {
            $data['assigned_at'] = now();
        }

        if (($data['status'] ?? null) === 'contacted') {
            $data['last_contacted_at'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->activities()->create([
            'user_id' => auth()->id(),
            'type' => 'created',
            'title' => __('admin.activities.lead_created_from_panel'),
        ]);

        if ($this->record->assigned_to) {
            $this->record->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'assigned',
                'title' => __('admin.activities.lead_assigned', [
                    'user' => $this->record->assignee?->name ?: '-',
                ]),
            ]);
        }
    }
}
