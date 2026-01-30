<?php

namespace App\Mail\Obstetrico;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectedRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente, $observacion;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($paciente, $observacion)
    {
        $this->paciente = $paciente;
        $this->observacion = $observacion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Los datos de su atención en Telesalud son:')
            ->markdown('emails.obstetricia.rejected-request');
    }
    /* public function build()
    {
        $this->subject('Los datos de su cita programada son:')
            ->markdown('emails.datoscita');
            foreach($this->adjuntos as $adjunto){
                $this->attach($this->ruta.$adjunto->getClientOriginalName());
            }
        return $this; 
    } */
}