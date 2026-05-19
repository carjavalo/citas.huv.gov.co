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
                ->subject('Verificación de Correo Electrónico')
                ->greeting('Cordial saludo,')
                ->line('Le solicitamos amablemente confirmar su dirección de correo electrónico haciendo clic en el botón a continuación.')
                ->line('De conformidad con lo establecido en la Ley Estatutaria 1581 de 2012 (Ley de Protección de Datos Personales) y sus decretos reglamentarios, al hacer clic en "Verificar mi correo", usted manifiesta su consentimiento expreso, previo, informado e inequívoco para que el Hospital Universitario del Valle "Evaristo García" E.S.E. realice la recolección, almacenamiento, uso, tratamiento y supresión de sus datos personales, conforme a nuestra Política de Tratamiento de Información.')
                ->action('Verificar mi correo', $url)
                ->line('Si usted no ha iniciado la creación de una cuenta en nuestro sistema institucional, por favor omitir y eliminar este mensaje.')
                ->salutation('Atentamente,');
        });
    }
}
