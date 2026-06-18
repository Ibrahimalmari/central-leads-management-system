<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use App\Mail\TestMail;
use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sendTestMail')
                ->label(__('admin.actions.send_test_mail'))
                ->form([
                    TextInput::make('email')
                        ->label(__('admin.fields.email'))
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    SystemSetting::applyMailConfig();

                    try {
                        Mail::to((string) $data['email'])->send(new TestMail);
                    } catch (Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title(__('admin.actions.test_mail_failed'))
                            ->body(__('admin.messages.mail_test_failed', ['message' => $exception->getMessage()]))
                            ->danger()
                            ->persistent()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title(__('admin.actions.test_mail_sent'))
                        ->body(__('admin.messages.mail_test_sent', ['email' => $data['email']]))
                        ->success()
                        ->send();
                }),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('admin.actions.settings_saved');
    }
}
