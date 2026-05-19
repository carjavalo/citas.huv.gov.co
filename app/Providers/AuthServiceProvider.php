<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super Admin tiene acceso a todas las opciones del sistema
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verificar correo electrónico')
                ->line('Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.')
                ->line('Al hacer clic en "Verificar mi correo", confirmas que aceptas y estás de acuerdo con nuestras políticas de tratamiento y protección de datos personales.')
                ->action('Verificar mi correo', $url)
                ->line('Si no has creado una cuenta, no es necesario realizar ninguna acción.');
        });
    }
}
