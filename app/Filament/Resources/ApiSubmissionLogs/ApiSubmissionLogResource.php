<?php

namespace App\Filament\Resources\ApiSubmissionLogs;

use App\Filament\Resources\ApiSubmissionLogs\Pages\ListApiSubmissionLogs;
use App\Filament\Resources\ApiSubmissionLogs\Schemas\ApiSubmissionLogForm;
use App\Filament\Resources\ApiSubmissionLogs\Tables\ApiSubmissionLogsTable;
use App\Models\ApiSubmissionLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ApiSubmissionLogResource extends Resource
{
    protected static ?string $model = ApiSubmissionLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.api_logs');
    }

    public static function getModelLabel(): string
    {
        return __('admin.models.api_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.models.api_logs');
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
        return ApiSubmissionLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiSubmissionLogsTable::configure($table);
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
            'index' => ListApiSubmissionLogs::route('/'),
        ];
    }
}
