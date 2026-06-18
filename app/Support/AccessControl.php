<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Form;
use App\Models\Lead;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AccessControl
{
    public static function user(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function isAdmin(?User $user = null): bool
    {
        return ($user ?? self::user())?->role === 'admin';
    }

    public static function isManager(?User $user = null): bool
    {
        return ($user ?? self::user())?->role === 'manager';
    }

    public static function isAgent(?User $user = null): bool
    {
        return ($user ?? self::user())?->role === 'agent';
    }

    public static function canUsePanelData(): bool
    {
        return in_array(self::user()?->role, ['admin', 'manager', 'agent'], true);
    }

    public static function scopeCompanies(Builder $query): Builder
    {
        $user = self::user();

        return match (true) {
            self::isAdmin($user) => $query,
            self::isManager($user) && filled($user->company_id) => $query->whereKey($user->company_id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function scopeSites(Builder $query): Builder
    {
        $user = self::user();

        return match (true) {
            self::isAdmin($user) => $query,
            self::isManager($user) && filled($user->company_id) => $query->where('company_id', $user->company_id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function scopeForms(Builder $query): Builder
    {
        $user = self::user();

        return match (true) {
            self::isAdmin($user) => $query,
            self::isManager($user) && filled($user->company_id) => $query->whereHas(
                'site',
                fn (Builder $siteQuery): Builder => $siteQuery->where('company_id', $user->company_id),
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function scopeLeads(Builder $query): Builder
    {
        $user = self::user();

        return match (true) {
            self::isAdmin($user) => $query,
            self::isManager($user) && filled($user->company_id) => $query->where('company_id', $user->company_id),
            self::isAgent($user) => $query->where('assigned_to', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function scopeAssignableUsers(Builder $query): Builder
    {
        $user = self::user();

        return match (true) {
            self::isAdmin($user) => $query->whereIn('role', ['manager', 'agent']),
            self::isManager($user) && filled($user->company_id) => $query
                ->where('company_id', $user->company_id)
                ->whereIn('role', ['manager', 'agent']),
            self::isAgent($user) => $query->whereKey($user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }

    public static function canViewCompany(Company $company): bool
    {
        $user = self::user();

        return self::isAdmin($user)
            || (self::isManager($user) && (int) $user->company_id === (int) $company->id);
    }

    public static function canViewSite(Site $site): bool
    {
        $user = self::user();

        return self::isAdmin($user)
            || (self::isManager($user) && (int) $user->company_id === (int) $site->company_id);
    }

    public static function canViewForm(Form $form): bool
    {
        $user = self::user();

        return self::isAdmin($user)
            || (self::isManager($user) && (int) $user->company_id === (int) $form->site?->company_id);
    }

    public static function canViewLead(Lead $lead): bool
    {
        $user = self::user();

        return self::isAdmin($user)
            || (self::isManager($user) && (int) $user->company_id === (int) $lead->company_id)
            || (self::isAgent($user) && (int) $lead->assigned_to === (int) $user->id);
    }
}
