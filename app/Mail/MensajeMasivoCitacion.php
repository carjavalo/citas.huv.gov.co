<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Citación enviada desde Mensajes Masivos: el texto ya llega armado con los
 * datos de la fila del Excel (especialidad, día, fecha, especialista, hora y sede).
 */
class MensajeMasivoCitacion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Reintentos ante un fallo puntual de SMTP. */
    public $tries = 3;

    /** Segundos entre reintentos. */
    public $backoff = 60;

    public $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->subject('Programación de su cita médica - Hospital Universitario del Valle')
            ->markdown('emails.mensaje-masivo-citacion');
    }
}
