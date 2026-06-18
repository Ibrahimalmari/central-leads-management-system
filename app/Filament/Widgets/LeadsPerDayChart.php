<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\DashboardLeadFilters;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class LeadsPerDayChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    protected string $color = 'primary';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('admin.stats.leads_last_14_days');
    }

    public function getDescription(): string
    {
        return __('admin.stats.leads_last_14_days_description');
    }

    protected function getData(): array
    {
        [$startDate, $endDate] = DashboardLeadFilters::chartPeriod($this->pageFilters);

        $counts = DashboardLeadFilters::apply(Lead::query(), $this->pageFilters)
            ->selectRaw('DATE(created_at) as lead_date, COUNT(*) as aggregate')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('lead_date')
            ->pluck('aggregate', 'lead_date');

        $labels = [];
        $data = [];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $key = $date->toDateString();

            $labels[] = $date->translatedFormat('d M');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('admin.models.leads'),
                    'data' => $data,
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
