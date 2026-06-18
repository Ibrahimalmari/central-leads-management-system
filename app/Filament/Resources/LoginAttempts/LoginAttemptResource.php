<?php

namespace App\Filament\Resources\LoginAttempts;

use App\Filament\Resources\LoginAttempts\Pages\ListLoginAttempts;
use App\Filament\Resources\LoginAttempts\Schemas\LoginAttemptForm;
use App\Filament\Resources\LoginAttempts\Tables\LoginAttemptsTable;
use App\Models\LoginAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LoginAttemptResource extends Resource
{
    protected static ?string $model = LoginAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 91;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.login_attempts');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.login_attempt');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.login_attempts');
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
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return LoginAttemptForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoginAttemptsTable::configure($table);
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
            'index' => ListLoginAttempts::route('/'),
        ];
    }
}
