<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return 'Sign in to your account';
    }

    public function getSubHeading(): string|Htmlable|null
    {
        return 'Use your password or a passkey to sign in';
    }
}
