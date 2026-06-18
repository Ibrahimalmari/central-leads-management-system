<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPasswordNotification;

class ResetPasswordNotification extends BaseResetPasswordNotification
{
    public ?string $url = null;

    protected function resetUrl($notifiable): string
    {
        return $this->url ?? parent::resetUrl($notifiable);
    }
}
