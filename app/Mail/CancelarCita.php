<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelarCita extends Mailable
{
    public $usuario, $especialidad;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($usuario, $especialidad)
    {
        $this->usuario = $usuario;
        $this->especialidad = $especialidad;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->subject('Cita cancelada')
        ->markdown('emails.cancelar-cita');
    }
}
