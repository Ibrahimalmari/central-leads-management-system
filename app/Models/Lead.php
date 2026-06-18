<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'in_progress' => 'In progress',
        'won' => 'Won',
        'lost' => 'Lost',
        'spam' => 'Spam',
        'closed' => 'Closed',
    ];

    public static function statusOptions(): array
    {
        return collect(array_keys(self::STATUSES))
            ->mapWithKeys(fn (string $status): array => [$status => __("admin.statuses.{$status}")])
            ->all();
    }

    protected $fillable = [
        'company_id',
        'site_id',
        'form_id',
        'form_key',
        'form_name',
        'form_type',
        'name',
        'email',
        'phone',
        'message',
        'page_url',
        'status',
        'assigned_to',
        'assigned_at',
        'last_contacted_at',
        'raw_data',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'assigned_at' => 'datetime',
            'last_contacted_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }
}
