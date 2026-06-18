<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LeadPipeline extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.lead-pipeline';

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.pipeline');
    }

    public function getTitle(): string
    {
        return __('admin.nav.pipeline');
    }

    public function getStatusColumns(): array
    {
        return collect(Lead::statusOptions())
            ->map(function (string $label, string $status): array {
                $leads = AccessControl::scopeLeads(Lead::query())
                    ->with(['site', 'assignee'])
                    ->where('status', $status)
                    ->latest()
                    ->limit(8)
                    ->get();

                return [
                    'status' => $status,
                    'label' => $label,
                    'count' => AccessControl::scopeLeads(Lead::query())->where('status', $status)->count(),
                    'leads' => $leads,
                ];
            })
            ->values()
            ->all();
    }

    public function leadUrl(Lead $lead): string
    {
        return LeadResource::getUrl('view', ['record' => $lead]);
    }
}
