<?php

namespace App\Mail;

use App\Models\CitacionMasiva;
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

    /** @var int|null Registro en citaciones_masivas, para marcarlo si el correo no se entrega. */
    public $citacionId;

    /** @var string|null Envío que encoló este correo: un reenvío posterior cambia el lote del registro. */
    public $lote;

    public function __construct($mensaje, $urlConfirmacion = null, $citacionId = null, $lote = null)
    {
        $this->mensaje         = $mensaje;
        $this->urlConfirmacion = $urlConfirmacion;
        $this->citacionId      = $citacionId;
        $this->lote            = $lote;
    }

    public function build()
    {
        return $this->subject('Programación de su cita médica - Hospital Universitario del Valle')
            ->markdown('emails.mensaje-masivo-citacion');
    }

    /** La cola agotó los reintentos: en el informe no debe figurar como enviada sin responder. */
    public function failed($e)
    {
        if ($this->citacionId) {
            CitacionMasiva::whereKey($this->citacionId)
                ->where('estado', CitacionMasiva::PENDIENTE)
                // Si la cita se reenvió mientras este correo esperaba, el fallo ya no aplica.
                ->when($this->lote, function ($q) {
                    $q->where('lote', $this->lote);
                })
                ->update(['estado' => CitacionMasiva::FALLIDO]);
        }
    }
}
