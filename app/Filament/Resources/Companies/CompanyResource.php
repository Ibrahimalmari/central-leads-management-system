<?php

namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\Pages\ViewCompany;
use App\Filament\Resources\Companies\RelationManagers\SitesRelationManager;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Resources\Companies\Schemas\CompanyInfolist;
use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.companies');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.company');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.companies');
    }

    public static function canViewAny(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function canCreate(): bool
    {
        return AccessControl::isAdmin();
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof Company && AccessControl::canViewCompany($record);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Company && AccessControl::canViewCompany($record);
    }

    public static function canDelete(Model $record): bool
    {
        return AccessControl::isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return AccessControl::isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return AccessControl::scopeCompanies(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompanyInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view' => ViewCompany::route('/{record}'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
