<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\UserActivity;

class LogUserLogin
{
    public function handle(Login $event)
    {
        UserActivity::registrar(
            $event->user->id,
            'login',
            'Usuario inició sesión',
            'autenticacion',
            'login'
        );
    }
}
