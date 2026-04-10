<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Acessar o Sistema';
    }

    public function getSubHeading(): ?string
    {
        return 'Elite Locadora — Gestão inteligente de frotas';
    }
}
