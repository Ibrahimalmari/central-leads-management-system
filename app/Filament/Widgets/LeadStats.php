<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use App\Support\DashboardLeadFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LeadStats extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $leadQuery = fn () => DashboardLeadFilters::apply(Lead::query(), $this->pageFilters);

        $statusCounts = $leadQuery()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count, $status) => __("admin.statuses.{$status}").': '.$count)
            ->implode(' | ');

        $total = $leadQuery()->count();
        $new = $leadQuery()->where('status', 'new')->count();
        $followUp = $leadQuery()->whereIn('status', ['contacted', 'in_progress'])->count();
        $won = $leadQuery()->where('status', 'won')->count();
        $conversionRate = $total > 0 ? round(($won / $total) * 100, 1).'%' : '0%';

        return [
            Stat::make(__('admin.stats.total_leads'), $total)
                ->description(__('admin.stats.current_filters'))
                ->color('primary')
                ->icon(Heroicon::OutlinedChartBar),
            Stat::make(__('admin.stats.new_leads'), $new)
                ->description(__('admin.stats.waiting_for_follow_up'))
                ->color($new > 0 ? 'warning' : 'success')
                ->icon(Heroicon::OutlinedInbox),
            Stat::make(__('admin.stats.follow_up_leads'), $followUp)
                ->description(__('admin.stats.contact_or_progress'))
                ->color($followUp > 0 ? 'info' : 'gray')
                ->icon(Heroicon::OutlinedPhoneArrowUpRight),
            Stat::make(__('admin.stats.conversion_rate'), $conversionRate)
                ->description(__('admin.stats.won_count', ['count' => $won]))
                ->color($won > 0 ? 'success' : 'gray')
                ->icon(Heroicon::OutlinedArrowTrendingUp),
            Stat::make(__('admin.stats.leads_by_status'), $statusCounts ?: __('admin.stats.no_leads_yet'))
                ->description(__('admin.stats.pipeline_snapshot'))
                ->color('gray')
                ->icon(Heroicon::OutlinedChartPie),
        ];
    }
}
