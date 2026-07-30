<?php

namespace App\Console\Commands;

use App\Mail\ReenvioCitacion;
use App\Models\solicitudes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * Diagnostica y repara las solicitudes que quedaron atascadas en estado
 * 'Procesando' ("En proceso" en la vista /consulta/solicitudes).
 *
 * Una solicitud pasa a 'Procesando' cuando un consultor abre el modal de
 * agendamiento, y solo pasa a 'Agendado' cuando el correo de citación se envía
 * correctamente. Si el envío falla o el consultor abandona el modal, la
 * solicitud queda atascada.
 */
class DiagnosticarSolicitudesProcesando extends Command
{
    protected $signature = 'solicitudes:procesando
                            {--reparar        : Aplica los cambios de estado (sin esta opción solo muestra el diagnóstico)}
                            {--reenviar       : Reenvía el certificado de citación a los casos NOTIFICADO (requiere --reparar)}
                            {--id=*           : Limita la operación a los IDs indicados}
                            {--dias=          : Solo solicitudes actualizadas hace más de N días}';

    protected $description = 'Diagnostica y repara solicitudes atascadas en estado "Procesando" (En proceso)';

    /** Estados a los que es seguro devolver una solicitud no notificada. */
    private const ESTADOS_RETORNO = ['Pendiente', 'Espera'];

    public function handle()
    {
        $query = solicitudes::where('solicitudes.estado', 'Procesando')
            ->leftJoin('users', 'solicitudes.pacid', '=', 'users.id')
            ->leftJoin('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
            ->select([
                'solicitudes.id',
                'solicitudes.pacid',
                'solicitudes.solnum',
                'solicitudes.estado_anterior',
                'solicitudes.certfdo_cita',
                'solicitudes.solicitud_mensaje_agendamiento',
                'solicitudes.usercod',
                'solicitudes.created_at',
                'solicitudes.updated_at',
                'users.name',
                'users.apellido1',
                'users.apellido2',
                'users.email',
                'users.ndocumento',
                'servicios.servnomb',
            ]);

        if ($ids = $this->option('id')) {
            $query->whereIn('solicitudes.id', $ids);
        }

        if ($dias = $this->option('dias')) {
            $query->where('solicitudes.updated_at', '<', now()->subDays((int) $dias));
        }

        $solicitudes = $query->orderBy('solicitudes.id')->get();

        if ($solicitudes->isEmpty()) {
            $this->info('No hay solicitudes en estado "Procesando".');
            return self::SUCCESS;
        }

        $this->line('');
        $this->info('=== SOLICITUDES ATASCADAS EN "EN PROCESO" ('.$solicitudes->count().') ===');
        $this->line('');

        $notificadas = [];  // El correo sí se envió: corresponde marcarlas Agendado
        $sinAgendar  = [];  // Nunca se notificó: deben volver a la cola
        $filas       = [];

        foreach ($solicitudes as $sol) {
            $certificado = $sol->certfdo_cita;
            $existeCert  = $certificado && file_exists(public_path($certificado));

            // certfdo_cita solo se escribe en el mismo UPDATE que pone
            // estado='Agendado' dentro de cita(). Si tiene valor, el correo de
            // citación llegó a enviarse en algún momento.
            if ($certificado) {
                $clasificacion = $existeCert ? 'NOTIFICADO' : 'NOTIFICADO (sin archivo)';
                $accion        = 'estado -> Agendado';
                $notificadas[] = $sol;
            } else {
                $destino       = in_array($sol->estado_anterior, self::ESTADOS_RETORNO, true)
                    ? $sol->estado_anterior
                    : 'Pendiente';
                $clasificacion = 'SIN AGENDAR';
                $accion        = 'estado -> '.$destino;
                $sinAgendar[]  = ['sol' => $sol, 'destino' => $destino];
            }

            $filas[] = [
                $sol->id,
                trim($sol->name.' '.$sol->apellido1),
                $sol->ndocumento,
                mb_strimwidth((string) $sol->servnomb, 0, 22, '…'),
                $sol->estado_anterior ?? '—',
                $existeCert ? 'sí' : ($certificado ? 'ruta rota' : 'no'),
                (string) $sol->updated_at,
                $clasificacion,
                $accion,
            ];
        }

        $this->table(
            ['ID', 'Paciente', 'Documento', 'Servicio', 'Est.ant.', 'Cert.', 'Actualizado', 'Clasificación', 'Acción'],
            $filas
        );

        $this->line('');
        $this->info('RESUMEN');
        $this->line('  NOTIFICADO  : '.count($notificadas).'  (se envió la citación; solo falta el estado)');
        $this->line('  SIN AGENDAR : '.count($sinAgendar).'  (nunca se notificó; vuelven a la cola)');
        $this->line('');

        if (count($sinAgendar) > 0) {
            $this->warn('IMPORTANTE: las solicitudes SIN AGENDAR no se pueden marcar como "Agendado".');
            $this->warn('La fecha y la hora de la cita nunca se guardan en la base de datos (solo viajan');
            $this->warn('en el correo), por lo que no existe dato con el cual reconstruir la citación.');
            $this->warn('Deben volver a "Pendiente"/"Espera" para que un consultor las agende de nuevo.');
            $this->line('');
        }

        if (!$this->option('reparar')) {
            $this->comment('Modo diagnóstico. Para aplicar los cambios ejecute:');
            $this->comment('  php artisan solicitudes:procesando --reparar');
            return self::SUCCESS;
        }

        // ---------- Reparación ----------
        $this->line('');
        $this->info('Aplicando cambios...');

        $agendadas = 0;
        $devueltas = 0;
        $enviados  = 0;
        $fallidos  = 0;

        DB::transaction(function () use ($notificadas, $sinAgendar, &$agendadas, &$devueltas) {
            foreach ($notificadas as $sol) {
                solicitudes::where('id', $sol->id)->update([
                    'estado'          => 'Agendado',
                    'estado_anterior' => null,
                ]);
                $agendadas++;
            }

            foreach ($sinAgendar as $item) {
                solicitudes::where('id', $item['sol']->id)->update([
                    'estado'          => $item['destino'],
                    'estado_anterior' => null,
                ]);
                $devueltas++;
            }
        });

        $this->info("  {$agendadas} solicitud(es) marcadas como 'Agendado'.");
        $this->info("  {$devueltas} solicitud(es) devueltas a la cola.");

        // El reenvío de correos es una acción externa e irreversible: solo se
        // ejecuta si se pide explícitamente.
        if ($this->option('reenviar')) {
            $this->line('');
            $this->info('Reenviando certificado de citación...');

            foreach ($notificadas as $sol) {
                if (!$sol->certfdo_cita || !file_exists(public_path($sol->certfdo_cita))) {
                    $this->warn("  ID {$sol->id}: sin certificado en disco, no se reenvía.");
                    $fallidos++;
                    continue;
                }

                try {
                    $paciente = trim($sol->name.' '.$sol->apellido1.' '.$sol->apellido2);
                    Mail::to($sol->email)->send(new ReenvioCitacion(
                        $paciente,
                        $sol->servnomb,
                        public_path($sol->certfdo_cita),
                        $sol->solicitud_mensaje_agendamiento
                    ));
                    $this->info("  ID {$sol->id}: reenviado a {$sol->email}");
                    $enviados++;
                } catch (\Throwable $th) {
                    $this->error("  ID {$sol->id}: error al reenviar - ".$th->getMessage());
                    $fallidos++;
                }
            }

            $this->line('');
            $this->info("  Correos reenviados: {$enviados}   Fallidos: {$fallidos}");
        }

        $this->line('');
        $this->info('Listo.');

        return self::SUCCESS;
    }
}
