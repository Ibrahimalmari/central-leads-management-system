<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        Notification::make()
            ->title(__('admin.actions.login_success_title'))
            ->body(__('admin.actions.login_success_body'))
            ->success()
            ->send();

        return redirect()->intended(Filament::getUrl());
    }
}
