<?php

namespace App\Filament\Resources\Sites;

use App\Filament\Resources\Sites\Pages\CreateSite;
use App\Filament\Resources\Sites\Pages\EditSite;
use App\Filament\Resources\Sites\Pages\ListSites;
use App\Filament\Resources\Sites\Pages\ViewSite;
use App\Filament\Resources\Sites\Schemas\SiteForm;
use App\Filament\Resources\Sites\Schemas\SiteInfolist;
use App\Filament\Resources\Sites\Tables\SitesTable;
use App\Models\Site;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteResource extends Resource
{
    protected static ?string $model = Site::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.sites');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.site');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.sites');
    }

    public static function canViewAny(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function canCreate(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof Site && AccessControl::canViewSite($record);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Site && AccessControl::canViewSite($record);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Site && AccessControl::canViewSite($record);
    }

    public static function canDeleteAny(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function getEloquentQuery(): Builder
    {
        return AccessControl::scopeSites(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return SiteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'view' => ViewSite::route('/{record}'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }
}
