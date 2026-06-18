<?php

namespace App\Filament\Resources\SystemSettings;

use App\Filament\Resources\SystemSettings\Pages\EditSystemSetting;
use App\Filament\Resources\SystemSettings\Pages\ListSystemSettings;
use App\Filament\Resources\SystemSettings\Schemas\SystemSettingForm;
use App\Filament\Resources\SystemSettings\Tables\SystemSettingsTable;
use App\Models\SystemSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 99;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.system_settings');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.system_setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.system_settings');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return SystemSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemSettingsTable::configure($table);
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
            'index' => ListSystemSettings::route('/'),
            'edit' => EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
