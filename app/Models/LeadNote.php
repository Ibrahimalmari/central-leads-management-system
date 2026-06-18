<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'note',
    ];

    protected static function booted(): void
    {
        static::creating(function (LeadNote $note): void {
            $note->user_id = $note->user_id ?: auth()->id();
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
