<?php

namespace App\Listeners;

use App\Models\LoginAttempt;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Schema;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        if (! Schema::hasTable('login_attempts')) {
            return;
        }

        LoginAttempt::create([
            'user_id' => $event->user?->getAuthIdentifier(),
            'email' => $event->credentials['email'] ?? null,
            'successful' => false,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempted_at' => now(),
        ]);
    }
}
