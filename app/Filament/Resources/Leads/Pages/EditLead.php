<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected array $trackedChanges = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->trackedChanges = [];

        foreach (['status', 'assigned_to'] as $field) {
            if (($data[$field] ?? null) != $this->record->{$field}) {
                $this->trackedChanges[$field] = [
                    'old' => $this->record->{$field},
                    'new' => $data[$field] ?? null,
                ];
            }
        }

        if (array_key_exists('assigned_to', $this->trackedChanges)) {
            $data['assigned_at'] = filled($data['assigned_to'] ?? null) ? now() : null;
        }

        if (
            array_key_exists('status', $this->trackedChanges) &&
            ($data['status'] ?? null) === 'contacted'
        ) {
            $data['last_contacted_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (array_key_exists('status', $this->trackedChanges)) {
            $this->record->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'status_changed',
                'title' => __('admin.activities.status_changed', [
                    'old' => __("admin.statuses.{$this->trackedChanges['status']['old']}"),
                    'new' => __("admin.statuses.{$this->trackedChanges['status']['new']}"),
                ]),
                'changes' => $this->trackedChanges['status'],
            ]);
        }

        if (array_key_exists('assigned_to', $this->trackedChanges)) {
            $this->record->load('assignee');

            $this->record->activities()->create([
                'user_id' => auth()->id(),
                'type' => 'assigned',
                'title' => __('admin.activities.lead_assigned', [
                    'user' => $this->record->assignee?->name ?: '-',
                ]),
                'changes' => $this->trackedChanges['assigned_to'],
            ]);
        }
    }
}
