<?php

namespace App\Mail\Obstetrico;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolvedRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $paciente, $observacion, $adjuntos;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($paciente, $observacion, $adjuntos)
    {
        $this->paciente = $paciente;
        $this->observacion = $observacion;
        $this->adjuntos = $adjuntos;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Los datos de su atención en Telesalud son:')
            ->markdown('emails.obstetricia.solved-request');
            if ($this->adjuntos) {
                foreach($this->adjuntos as $adjunto){
                    $this->attach($adjunto->getRealPath());
                }
            }

        return $this;
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
