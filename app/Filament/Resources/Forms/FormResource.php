<?php

namespace App\Filament\Resources\Forms;

use App\Filament\Resources\Forms\Pages\CreateForm;
use App\Filament\Resources\Forms\Pages\EditForm;
use App\Filament\Resources\Forms\Pages\ListForms;
use App\Filament\Resources\Forms\Pages\ViewForm;
use App\Filament\Resources\Forms\Schemas\FormForm;
use App\Filament\Resources\Forms\Schemas\FormInfolist;
use App\Filament\Resources\Forms\Tables\FormsTable;
use App\Models\Form;
use App\Support\AccessControl;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FormResource extends Resource
{
    protected static ?string $model = Form::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.forms');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.form');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.forms');
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
        return $record instanceof Form && AccessControl::canViewForm($record);
    }

    public static function canEdit(Model $record): bool
    {
        return $record instanceof Form && AccessControl::canViewForm($record);
    }

    public static function canDelete(Model $record): bool
    {
        return $record instanceof Form && AccessControl::canViewForm($record);
    }

    public static function canDeleteAny(): bool
    {
        return AccessControl::isAdmin() || AccessControl::isManager();
    }

    public static function getEloquentQuery(): Builder
    {
        return AccessControl::scopeForms(parent::getEloquentQuery());
    }

    public static function form(Schema $schema): Schema
    {
        return FormForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FormInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FormsTable::configure($table);
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
            'index' => ListForms::route('/'),
            'create' => CreateForm::route('/create'),
            'view' => ViewForm::route('/{record}'),
            'edit' => EditForm::route('/{record}/edit'),
        ];
    }
}
