<?php

namespace App\Listeners;

use App\Mail\MensajeMasivoCitacion;
use App\Models\CitacionMasiva;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

/**
 * Sella 'entregado_at' cuando el correo de una citación masiva sale de verdad.
 *
 * MessageSent se dispara cuando el servidor SMTP ya aceptó el mensaje, que es
 * lo que no se sabía: 'enviado_at' solo decía que la citación se había
 * encolado. El correo lleva la cabecera X-Citacion-Masiva-Id para saber a qué
 * registro corresponde.
 */
class MarcarCitacionEntregada
{
    public function handle(MessageSent $event)
    {
        $cabecera = $event->message->getHeaders()->get(MensajeMasivoCitacion::CABECERA_CITACION);
        if (!$cabecera) {
            return;
        }

        $id = (int) $cabecera->getFieldBody();
        if ($id <= 0) {
            return;
        }

        try {
            // Solo la primera entrega: un reenvío pone entregado_at en null otra vez.
            CitacionMasiva::whereKey($id)
                ->whereNull('entregado_at')
                ->update(['entregado_at' => now()]);
        } catch (\Throwable $th) {
            // El correo ya salió: un fallo aquí no debe reventar el envío.
            Log::warning('No se pudo marcar la citación como entregada', [
                'citacion' => $id,
                'error'    => $th->getMessage(),
            ]);
        }
    }
}
