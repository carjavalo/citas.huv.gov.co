<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Models\UserActivity;

class LogUserLogout
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            UserActivity::registrar(
                $event->user->id,
                'logout',
                'Usuario cerró sesión',
                'autenticacion',
                'logout'
            );
        }
    }
}
