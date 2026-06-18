<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\DashboardLeadFilters;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadsByStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected string $color = 'success';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('admin.stats.leads_by_status_chart');
    }

    protected function getData(): array
    {
        $counts = DashboardLeadFilters::apply(Lead::query(), $this->pageFilters)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statuses = Lead::statusOptions();

        return [
            'datasets' => [
                [
                    'data' => collect($statuses)
                        ->keys()
                        ->map(fn (string $status): int => (int) ($counts[$status] ?? 0))
                        ->values()
                        ->all(),
                    'backgroundColor' => [
                        '#2563eb',
                        '#0f766e',
                        '#d97706',
                        '#16a34a',
                        '#dc2626',
                        '#7c3aed',
                        '#475569',
                    ],
                ],
            ],
            'labels' => array_values($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
