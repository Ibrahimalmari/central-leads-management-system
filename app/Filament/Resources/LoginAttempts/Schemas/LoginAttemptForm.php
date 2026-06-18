<?php

namespace App\Filament\Resources\LoginAttempts\Schemas;

use Filament\Schemas\Schema;

class LoginAttemptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
