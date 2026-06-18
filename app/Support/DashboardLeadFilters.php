<?php

namespace App\Support;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DashboardLeadFilters
{
    /**
     * @param  Builder<Lead>  $query
     * @param  array<string, mixed>|null  $filters
     * @return Builder<Lead>
     */
    public static function apply(Builder $query, ?array $filters): Builder
    {
        $filters = self::normalize($filters);

        return AccessControl::scopeLeads($query)
            ->when($filters['from'], fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
            ->when($filters['until'], fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date))
            ->when($filters['company_id'], fn (Builder $query, int $id): Builder => $query->where('company_id', $id))
            ->when($filters['site_id'], fn (Builder $query, int $id): Builder => $query->where('site_id', $id))
            ->when($filters['form_id'], fn (Builder $query, int $id): Builder => $query->where('form_id', $id))
            ->when($filters['status'], fn (Builder $query, string $status): Builder => $query->where('status', $status))
            ->when($filters['assigned_to'], fn (Builder $query, int $id): Builder => $query->where('assigned_to', $id));
    }

    /**
     * @param  array<string, mixed>|null  $filters
     * @return array{from: ?string, until: ?string, company_id: ?int, site_id: ?int, form_id: ?int, status: ?string, assigned_to: ?int}
     */
    public static function normalize(?array $filters): array
    {
        return [
            'from' => filled($filters['from'] ?? null) ? (string) $filters['from'] : null,
            'until' => filled($filters['until'] ?? null) ? (string) $filters['until'] : null,
            'company_id' => filled($filters['company_id'] ?? null) ? (int) $filters['company_id'] : null,
            'site_id' => filled($filters['site_id'] ?? null) ? (int) $filters['site_id'] : null,
            'form_id' => filled($filters['form_id'] ?? null) ? (int) $filters['form_id'] : null,
            'status' => filled($filters['status'] ?? null) ? (string) $filters['status'] : null,
            'assigned_to' => filled($filters['assigned_to'] ?? null) ? (int) $filters['assigned_to'] : null,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $filters
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function chartPeriod(?array $filters): array
    {
        $filters = self::normalize($filters);

        $until = $filters['until'] ? Carbon::parse($filters['until']) : Carbon::today();
        $from = $filters['from'] ? Carbon::parse($filters['from']) : $until->copy()->subDays(13);

        if ($from->gt($until)) {
            [$from, $until] = [$until, $from];
        }

        if ($from->diffInDays($until) > 45) {
            $from = $until->copy()->subDays(45);
        }

        return [$from->startOfDay(), $until->endOfDay()];
    }
}
