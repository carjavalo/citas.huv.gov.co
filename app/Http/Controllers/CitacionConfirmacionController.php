<?php

namespace App\Http\Controllers;

use App\Http\Livewire\MensajesMasivos\Consulta as MensajesMasivos;
use App\Models\CitacionMasiva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Vista pública (sin iniciar sesión) a la que llega el paciente desde el botón
 * "Ir a confirmar" del correo de Mensajes Masivos. El token del enlace es la
 * única credencial: identifica una sola citación.
 */
class CitacionConfirmacionController extends Controller
{
    public function mostrar($token)
    {
        $citacion = CitacionMasiva::where('token', $token)->first();

        return response()->view('citacion.confirmar', [
            'citacion'  => $citacion,
            'direccion' => $citacion ? $this->direccion($citacion) : null,
        ], $citacion ? 200 : 404);
    }

    public function responder(Request $request, $token)
    {
        $citacion = CitacionMasiva::where('token', $token)->firstOrFail();

        $datos = $request->validate([
            'respuesta' => ['required', Rule::in([CitacionMasiva::ASISTIRA, CitacionMasiva::NO_ASISTIRA])],
        ]);

        $citacion->update([
            'estado'        => $datos['respuesta'],
            'respondido_at' => now(),
        ]);

        return redirect()->route('citacion.confirmar', $token)->with('respuesta_guardada', true);
    }

    private function direccion(CitacionMasiva $citacion)
    {
        return MensajesMasivos::SEDES[$citacion->sede]['direccion'] ?? MensajesMasivos::SEDES['cali']['direccion'];
    }
}
