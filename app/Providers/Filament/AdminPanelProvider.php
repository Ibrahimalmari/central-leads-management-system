<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\LeadStats;
use App\Filament\Widgets\LeadsBySiteChart;
use App\Filament\Widgets\LeadsByStatusChart;
use App\Filament\Widgets\LeadsPerDayChart;
use App\Filament\Widgets\TopForms;
use App\Filament\Widgets\TopSites;
use App\Http\Middleware\SetLocale;
use App\Models\SystemSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(fn () => SystemSetting::brandName())
            ->brandLogo(fn () => SystemSetting::logoUrl())
            ->brandLogoHeight('3rem')
            ->favicon(fn () => SystemSetting::faviconUrl())
            ->font('Cairo', url: 'https://fonts.bunny.net/css?family=cairo:400,500,600,700')
            ->login()
            ->passwordReset()
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): HtmlString => new HtmlString(view('filament.theme-overrides')->render()),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
                fn (): HtmlString => new HtmlString(view('filament.auth-intro', ['mode' => 'login'])->render()),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): HtmlString => new HtmlString(view('filament.auth-language-switcher')->render()),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_BEFORE,
                fn (): HtmlString => new HtmlString(view('filament.auth-intro', ['mode' => 'password'])->render()),
            )
            ->renderHook(
                PanelsRenderHook::AUTH_PASSWORD_RESET_REQUEST_FORM_AFTER,
                fn (): HtmlString => new HtmlString(view('filament.auth-language-switcher')->render()),
            )
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => __('admin.switch_to_arabic'))
                    ->icon(Heroicon::OutlinedLanguage)
                    ->url(fn () => route('language.switch', ['locale' => 'ar']))
                    ->visible(fn () => app()->getLocale() !== 'ar'),
                MenuItem::make()
                    ->label(fn () => __('admin.switch_to_english'))
                    ->icon(Heroicon::OutlinedLanguage)
                    ->url(fn () => route('language.switch', ['locale' => 'en']))
                    ->visible(fn () => app()->getLocale() !== 'en'),
            ])
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode()
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                LeadStats::class,
                LeadsPerDayChart::class,
                LeadsByStatusChart::class,
                LeadsBySiteChart::class,
                TopSites::class,
                TopForms::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
