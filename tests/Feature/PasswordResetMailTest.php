<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Notifications\ResetPasswordNotification;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_password_reset_notification_is_sent_without_queue(): void
    {
        $notification = app(FilamentResetPasswordNotification::class, [
            'token' => 'test-token',
        ]);

        $this->assertInstanceOf(ResetPasswordNotification::class, $notification);
        $this->assertNotInstanceOf(ShouldQueue::class, $notification);
    }

    public function test_system_mail_settings_configure_smtp_scheme(): void
    {
        SystemSetting::current()->update([
            'mail_host' => 'smtp.example.com',
            'mail_port' => 465,
            'mail_encryption' => 'ssl',
            'mail_username' => 'user@example.com',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'Central Leads',
        ]);

        SystemSetting::applyMailConfig();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('noreply@example.com', config('mail.from.address'));
    }
}
