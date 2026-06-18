<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    protected $fillable = [
        'app_name',
        'logo_path',
        'notify_new_leads',
        'notification_emails',
        'whatsapp_notifications_enabled',
        'whatsapp_webhook_url',
        'mail_host',
        'mail_port',
        'mail_encryption',
        'mail_username',
        'mail_password',
        'mail_from_address',
        'mail_from_name',
    ];

    protected function casts(): array
    {
        return [
            'notify_new_leads' => 'boolean',
            'notification_emails' => 'array',
            'whatsapp_notifications_enabled' => 'boolean',
            'mail_password' => 'encrypted',
        ];
    }

    public static function current(): self
    {
        if (! Schema::hasTable('system_settings')) {
            return new self([
                'app_name' => config('app.name', 'Central Leads Management System'),
            ]);
        }

        return self::query()->firstOrCreate(
            ['id' => 1],
            ['app_name' => null],
        );
    }

    public static function brandName(): string
    {
        if (filled(self::current()->app_name)) {
            return self::current()->app_name;
        }

        $locale = request()->hasSession()
            ? (request()->session()->get('locale') ?: app()->getLocale())
            : app()->getLocale();

        return trans('admin.brand', locale: $locale);
    }

    public static function logoUrl(): string
    {
        $logoPath = self::current()->logo_path;

        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $version = self::current()->updated_at?->timestamp ?? 1;

            return url('/system-logo').'?v='.$version;
        }

        return asset('images/central-leads-logo.svg');
    }

    public static function faviconUrl(): string
    {
        $version = self::current()->updated_at?->timestamp ?? 1;

        return url('/system-favicon').'?v='.$version;
    }

    public static function applyMailConfig(): void
    {
        if (! Schema::hasTable('system_settings')) {
            return;
        }

        $settings = self::current();

        if (! $settings->mail_host) {
            return;
        }

        $scheme = match ($settings->mail_encryption) {
            'ssl' => 'smtps',
            'tls' => 'smtp',
            default => null,
        };

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.host' => $settings->mail_host,
            'mail.mailers.smtp.port' => $settings->mail_port ?: config('mail.mailers.smtp.port'),
            'mail.mailers.smtp.encryption' => $settings->mail_encryption ?: config('mail.mailers.smtp.encryption'),
            'mail.mailers.smtp.username' => $settings->mail_username,
            'mail.mailers.smtp.password' => $settings->mail_password,
            'mail.from.address' => $settings->mail_from_address ?: config('mail.from.address'),
            'mail.from.name' => $settings->mail_from_name ?: config('mail.from.name'),
        ]);

        Mail::forgetMailers();
    }
}
