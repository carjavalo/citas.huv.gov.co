<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReagendarCita extends Mailable
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
        ->subject('Su solicitud está siendo procesada nuevamente')
        ->markdown('emails.reagendar-cita');
    }
}
