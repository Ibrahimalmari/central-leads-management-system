<?php

namespace App\Listeners;

use App\Models\LoginAttempt;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Schema;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if (! Schema::hasTable('login_attempts')) {
            return;
        }

        LoginAttempt::create([
            'user_id' => $event->user->getAuthIdentifier(),
            'email' => $event->user->email,
            'successful' => true,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'attempted_at' => now(),
        ]);
    }
}
