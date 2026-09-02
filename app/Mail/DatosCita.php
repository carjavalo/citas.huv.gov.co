<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notificación de cita agendada.
 *
 * Se envía en cola: el envío por SMTP con los PDF adjuntos tardaba varios
 * segundos dentro de la petición de Livewire y, si el servidor cortaba la
 * petición por timeout, la solicitud quedaba atascada en estado 'Procesando'.
 *
 * IMPORTANTE: $adjuntos son RUTAS ABSOLUTAS en disco, no objetos de subida.
 * Un TemporaryUploadedFile de Livewire no sobrevive a la serialización del job
 * (y el archivo temporal se borra), por eso el componente guarda primero los
 * archivos y pasa las rutas ya definitivas.
 */
class DatosCita extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** Reintentos antes de darse por vencido (SMTP puede fallar puntualmente). */
    public $tries = 3;

    /** Segundos de espera entre reintentos. */
    public $backoff = 60;

    public $fecha, $hora, $ubicacion, $reserva, $paciente, $mensaje;

    /** @var string[] Rutas absolutas de los archivos a adjuntar. */
    public $adjuntos;

    /**
     * @param  string[]  $adjuntos  Rutas absolutas de los PDF ya almacenados.
     */
    public function __construct($fecha, $hora, $ubicacion, array $adjuntos, $reserva, $paciente, $mensaje)
    {
        $this->fecha     = $fecha;
        $this->hora      = $hora;
        $this->ubicacion = $ubicacion;
        $this->adjuntos  = array_values($adjuntos);
        $this->reserva   = $reserva;
        $this->paciente  = $paciente;
        $this->mensaje   = $mensaje;
    }

    public function build()
    {
        $this->subject('Los datos de su cita programada son:')
            ->markdown('emails.datoscita');

        foreach ($this->adjuntos as $adjunto) {
            // El worker corre con otro directorio de trabajo que Apache, así que
            // solo se adjunta lo que exista realmente en la ruta indicada.
            if (is_string($adjunto) && $adjunto !== '' && file_exists($adjunto)) {
                $this->attach($adjunto);
            }
        }

        return $this;
    }
}
