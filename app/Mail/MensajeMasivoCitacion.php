<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Citación enviada desde Mensajes Masivos: el texto ya llega armado con los
 * datos de la fila del Excel (especialidad, día, fecha, especialista, hora y sede).
 * Al final lleva el botón "Ir a confirmar" para que el paciente responda.
 */
class MensajeMasivoCitacion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Reintentos ante un fallo puntual de SMTP. */
    public $tries = 3;

    /** Segundos entre reintentos. */
    public $backoff = 60;

    public $mensaje;

    /** @var string|null Enlace a la vista donde el paciente confirma o no su asistencia. */
    public $urlConfirmacion;

    public function __construct($mensaje, $urlConfirmacion = null)
    {
        $this->mensaje         = $mensaje;
        $this->urlConfirmacion = $urlConfirmacion;
    }

    public function build()
    {
        return $this->subject('Programación de su cita médica - Hospital Universitario del Valle')
            ->markdown('emails.mensaje-masivo-citacion');
    }
}
