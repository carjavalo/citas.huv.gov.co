<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DatosCita extends Mailable
{
    public $fecha, $hora, $ubicacion, $medico, $reserva, $paciente, $mensaje, $adjuntos, $ruta;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fecha, $hora, $ubicacion, $adjuntos, $reserva, $paciente, $mensaje, $ruta)
    {
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->ubicacion = $ubicacion;
        $this->adjuntos = $adjuntos;
        $this->reserva  = $reserva;
        $this->paciente = $paciente;
        $this->mensaje = $mensaje;
        $this->ruta = $ruta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Los datos de su cita programada son:')
            ->markdown('emails.datoscita');
            foreach($this->adjuntos as $adjunto){
                $this->attach($this->ruta.$adjunto->getClientOriginalName());
            }
        return $this; 
    }
}
