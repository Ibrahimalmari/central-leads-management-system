<?php

namespace App\Filament\Resources\Leads;

use App\Filament\Resources\Leads\Pages\CreateLead;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Filament\Resources\Leads\Pages\ViewLead;
use App\Filament\Resources\Leads\RelationManagers\ActivitiesRelationManager;
use App\Filament\Resources\Leads\RelationManagers\NotesRelationManager;
use App\Filament\Resources\Leads\Schemas\LeadForm;
use App\Filament\Resources\Leads\Schemas\LeadInfolist;
use App\Filament\Resources\Leads\Tables\LeadsTable;
use App\Models\Lead;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.leads');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.lead');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.leads');
    }

    public static function canViewAny(): bool
    {
        return AccessControl::canUsePanelData();
    }

    public static function canCreate(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function canView(Model $record): bool
    {
        return $record instanceof Lead && AccessControl::canViewLead($record);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Lead && AccessControl::canViewLead($record);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Lead
            && (AccessControl::isAdmin() || (AccessControl::isManager() && AccessControl::canViewLead($record)));
    }

    public static function canDeleteAny(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function getEloquentQuery(): Builder
    {
        return AccessControl::scopeLeads(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return LeadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LeadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            NotesRelationManager::class,
            ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLeads::route('/'),
            'create' => CreateLead::route('/create'),
            'view' => ViewLead::route('/{record}'),
            'edit' => EditLead::route('/{record}/edit'),
        ];
    }
}
