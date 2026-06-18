<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Models\Site;
use App\Support\AccessControl;
use App\Support\DashboardLeadFilters;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadsBySiteChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    protected string $color = 'warning';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('admin.stats.leads_by_site_chart');
    }

    protected function getData(): array
    {
        $counts = DashboardLeadFilters::apply(Lead::query(), $this->pageFilters)
            ->selectRaw('site_id, COUNT(*) as aggregate')
            ->whereNotNull('site_id')
            ->groupBy('site_id')
            ->orderByDesc('aggregate')
            ->limit(7)
            ->pluck('aggregate', 'site_id');

        $sites = AccessControl::scopeSites(Site::query())
            ->whereKey($counts->keys())
            ->pluck('name', 'id');

        return [
            'datasets' => [
                [
                    'label' => __('admin.fields.leads'),
                    'data' => $counts->map(fn ($count): int => (int) $count)->values()->all(),
                    'borderRadius' => 8,
                ],
            ],
            'labels' => $counts->keys()->map(fn ($siteId): string => (string) ($sites[$siteId] ?? '#'.$siteId))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
