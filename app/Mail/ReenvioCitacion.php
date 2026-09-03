<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Reenvía el certificado de citación ya generado para una solicitud.
 *
 * A diferencia de DatosCita, no reconstruye la fecha ni la hora (esos datos no
 * se almacenan en la base de datos): adjunta el certificado original, que es
 * donde el paciente encuentra los datos de la cita.
 */
class ReenvioCitacion extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Reintentos ante un fallo puntual de SMTP. */
    public $tries = 3;

    /** Segundos entre reintentos. */
    public $backoff = 60;

    public $paciente, $servicio, $mensaje;

    /** @var string Ruta ABSOLUTA del certificado (el worker corre en otro cwd). */
    public $rutaCertificado;

    public function __construct($paciente, $servicio, $rutaCertificado, $mensaje = null)
    {
        $this->paciente        = $paciente;
        $this->servicio        = $servicio;
        $this->rutaCertificado = $rutaCertificado;
        $this->mensaje         = $mensaje;
    }

    public function build()
    {
        $this->subject('Reenvío: los datos de su cita programada')
            ->markdown('emails.reenvio-citacion');

        if ($this->rutaCertificado && file_exists($this->rutaCertificado)) {
            $this->attach($this->rutaCertificado);
        }

        return $this;
    }
}
