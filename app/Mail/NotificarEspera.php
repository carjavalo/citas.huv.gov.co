<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificarEspera extends Mailable
{
    public $mensaje, $usuario, $fecha_solicitud;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mensaje, $usuario, $fecha_solicitud)
    {
        $this->mensaje = $mensaje;
        $this->usuario = $usuario;
        $this->fecha_solicitud = $fecha_solicitud;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->subject('Su solicitud se encuentra en lista de espera')
        ->markdown('emails.notificar-espera');
    }
}
