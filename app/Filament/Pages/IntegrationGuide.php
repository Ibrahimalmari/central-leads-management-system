<?php

namespace App\Filament\Pages;

use App\Models\Site;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;

class IntegrationGuide extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static ?int $navigationSort = 89;

    protected string $view = 'filament.pages.integration-guide';

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.integration_guide');
    }

    public function getTitle(): string
    {
        return __('admin.nav.integration_guide');
    }

    public static function canAccess(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public function getEndpoint(): string
    {
        return url('/api/leads');
    }

    public function getSites(): Collection
    {
        return AccessControl::scopeSites(Site::query())
            ->with('company')
            ->orderBy('name')
            ->get();
    }
}
