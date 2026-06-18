<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Site extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'url',
        'site_key',
        'api_key',
        'api_key_preview',
        'status',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (Site $site): void {
            $site->site_key = $site->site_key ?: Str::slug($site->name).'-'.Str::lower(Str::random(6));

            if (blank($site->getAttributes()['api_key'] ?? null)) {
                $site->api_key = self::generateApiKey();
            }
        });
    }

    public static function generateApiKey(): string
    {
        return 'sk_'.Str::random(48);
    }

    public static function hashApiKey(string $apiKey): string
    {
        return hash('sha256', $apiKey);
    }

    public static function previewApiKey(string $apiKey): string
    {
        return Str::substr($apiKey, 0, 8).'...'.Str::substr($apiKey, -4);
    }

    public function setApiKeyAttribute(?string $apiKey): void
    {
        if (! filled($apiKey)) {
            return;
        }

        $this->attributes['api_key'] = self::hashApiKey($apiKey);
        $this->attributes['api_key_preview'] = self::previewApiKey($apiKey);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
