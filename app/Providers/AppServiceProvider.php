<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Models\SystemSetting;
use App\Notifications\ResetPasswordNotification;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Auth\Notifications\ResetPassword as FilamentResetPasswordNotification;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginResponseContract::class, LoginResponse::class);
        $this->app->bind(
            FilamentResetPasswordNotification::class,
            fn ($app, array $parameters): ResetPasswordNotification => new ResetPasswordNotification(
                (string) ($parameters['token'] ?? ''),
            ),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        SystemSetting::applyMailConfig();

        RateLimiter::for('lead-api', function (Request $request) {
            $key = $request->bearerToken() ?: $request->header('X-API-Key') ?: $request->ip();

            return Limit::perMinute((int) env('LEAD_API_RATE_LIMIT', 30))->by(sha1((string) $key));
        });

        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Failed::class, LogFailedLogin::class);
    }
}
