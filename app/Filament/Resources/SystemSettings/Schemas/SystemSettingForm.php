<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('app_name')
                    ->label(__('admin.fields.system_name'))
                    ->placeholder(__('admin.brand'))
                    ->maxLength(255),
                FileUpload::make('logo_path')
                    ->label(__('admin.fields.system_logo'))
                    ->disk('public')
                    ->directory('system')
                    ->image()
                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                    ->imagePreviewHeight('64')
                    ->maxSize(512)
                    ->deletable()
                    ->openable()
                    ->downloadable()
                    ->deleteUploadedFileUsing(function (?string $file): void {
                        if ($file && Storage::disk('public')->exists($file)) {
                            Storage::disk('public')->delete($file);
                        }
                    })
                    ->helperText(__('admin.fields.system_logo_hint'))
                    ->columnSpanFull(),
                Toggle::make('notify_new_leads')
                    ->label(__('admin.fields.notify_new_leads')),
                Repeater::make('notification_emails')
                    ->label(__('admin.fields.notification_emails'))
                    ->schema([
                        TextInput::make('email')
                            ->label(__('admin.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->addActionLabel(__('admin.actions.add_email'))
                    ->columnSpanFull(),
                Toggle::make('whatsapp_notifications_enabled')
                    ->label(__('admin.fields.whatsapp_notifications_enabled')),
                TextInput::make('whatsapp_webhook_url')
                    ->label(__('admin.fields.whatsapp_webhook_url'))
                    ->url()
                    ->maxLength(2048)
                    ->helperText(__('admin.fields.whatsapp_webhook_hint'))
                    ->columnSpanFull(),
                TextInput::make('mail_host')
                    ->label(__('admin.fields.mail_host'))
                    ->maxLength(255),
                TextInput::make('mail_port')
                    ->label(__('admin.fields.mail_port'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(65535),
                Select::make('mail_encryption')
                    ->label(__('admin.fields.mail_encryption'))
                    ->options([
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                    ]),
                TextInput::make('mail_username')
                    ->label(__('admin.fields.mail_username'))
                    ->maxLength(255),
                TextInput::make('mail_password')
                    ->label(__('admin.fields.mail_password'))
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state): bool => filled($state))
                    ->helperText(__('admin.fields.mail_password_hint')),
                TextInput::make('mail_from_address')
                    ->label(__('admin.fields.mail_from_address'))
                    ->email()
                    ->maxLength(255),
                TextInput::make('mail_from_name')
                    ->label(__('admin.fields.mail_from_name'))
                    ->maxLength(255),
            ]);
    }
}
