<?php

namespace App\Console\Commands;

use App\Http\Livewire\Citas\ConsultaGeneral;
use App\Mail\ReenvioCitacion;
use App\Models\solicitudes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Diagnostica y repara las solicitudes que quedaron atascadas en estado
 * 'Procesando' ("En proceso" en la vista /consulta/solicitudes).
 *
 * Una solicitud pasa a 'Procesando' cuando un consultor abre el modal de
 * agendamiento, y solo pasa a 'Agendado' cuando la cita se guarda. Si el
 * consultor abandona el modal, cierra la pestaña o pierde la sesión, la
 * solicitud queda bloqueada indefinidamente.
 *
 * Con --auto este comando actúa como watchdog desatendido: lo ejecuta el
 * scheduler cada 15 minutos y libera lo que lleve demasiado tiempo bloqueado.
 */
class DiagnosticarSolicitudesProcesando extends Command
{
    protected $signature = 'solicitudes:procesando
                            {--reparar        : Aplica los cambios de estado (sin esta opción solo muestra el diagnóstico)}
                            {--reenviar       : Reenvía el certificado de citación a los casos NOTIFICADO (requiere --reparar)}
                            {--id=*           : Limita la operación a los IDs indicados}
                            {--dias=          : Solo solicitudes bloqueadas hace más de N días}
                            {--minutos=       : Solo solicitudes bloqueadas hace más de N minutos}
                            {--auto           : Modo watchdog desatendido: repara sin interacción y registra en el log}';

    protected $description = 'Diagnostica y repara solicitudes atascadas en estado "Procesando" (En proceso)';

    /** Estados a los que es seguro devolver una solicitud no notificada. */
    private const ESTADOS_RETORNO = ['Pendiente', 'Espera'];

    /** Minutos de bloqueo tras los que el watchdog considera abandonado el agendamiento. */
    private const MINUTOS_AUTO = 30;

    public function handle()
    {
        $auto = (bool) $this->option('auto');

        if ($auto) {
            $this->limpiarBloqueosCaducados();
        }

        $solicitudes = $this->consultar($auto);

        if ($solicitudes->isEmpty()) {
            if (!$auto) {
                $this->info('No hay solicitudes en estado "Procesando" que cumplan el filtro.');
                $this->line('');
                $this->revisarCola();
            }
            return self::SUCCESS;
        }

        [$notificadas, $sinAgendar, $filas] = $this->clasificar($solicitudes);

        if ($auto) {
            return $this->ejecutarWatchdog($notificadas, $sinAgendar);
        }

        $this->line('');
        $this->info('=== SOLICITUDES ATASCADAS EN "EN PROCESO" ('.$solicitudes->count().') ===');
        $this->line('');

        $this->table(
            ['ID', 'Paciente', 'Documento', 'Servicio', 'Est.ant.', 'Cert.', 'Fecha cita', 'Bloqueada desde', 'Clasificacion', 'Accion'],
            $filas
        );

        $this->line('');
        $this->info('RESUMEN');
        $this->line('  NOTIFICADO  : '.count($notificadas).'  (la cita se guardó; solo falta el estado)');
        $this->line('  SIN AGENDAR : '.count($sinAgendar).'  (nunca se agendó; vuelven a la cola)');
        $this->line('');

        if (count($sinAgendar) > 0) {
            $this->warn('Las solicitudes SIN AGENDAR no se pueden marcar como "Agendado": el consultor');
            $this->warn('nunca llegó a guardar la cita, así que no hay fecha ni hora que reconstruir.');
            $this->warn('Vuelven a "Pendiente"/"Espera" para que un consultor las agende de nuevo.');
            $this->line('');
        }

        $this->revisarCola();

        if (!$this->option('reparar')) {
            $this->comment('Modo diagnóstico. Para aplicar los cambios ejecute:');
            $this->comment('  php artisan solicitudes:procesando --reparar');
            return self::SUCCESS;
        }

        // ---------- Reparación ----------
        $this->line('');
        $this->info('Aplicando cambios...');

        [$agendadas, $devueltas] = $this->reparar($notificadas, $sinAgendar);

        $this->info("  {$agendadas} solicitud(es) marcadas como 'Agendado'.");
        $this->info("  {$devueltas} solicitud(es) devueltas a la cola.");

        // El reenvío de correos es una acción externa e irreversible: solo se
        // ejecuta si se pide explícitamente.
        if ($this->option('reenviar')) {
            $this->reenviar($notificadas);
        }

        $this->line('');
        $this->info('Listo.');

        return self::SUCCESS;
    }

    /**
     * Solicitudes en 'Procesando' que cumplen los filtros de antigüedad e ID.
     *
     * La antigüedad se mide sobre procesando_desde, que marca el instante en que
     * se abrió el modal. Las solicitudes anteriores a esa columna no lo tienen,
     * así que se recurre a updated_at como aproximación.
     */
    private function consultar(bool $auto)
    {
        $query = solicitudes::where('solicitudes.estado', 'Procesando')
            ->leftJoin('users', 'solicitudes.pacid', '=', 'users.id')
            ->leftJoin('servicios', 'solicitudes.espec', '=', 'servicios.servcod')
            ->select([
                'solicitudes.id',
                'solicitudes.pacid',
                'solicitudes.solnum',
                'solicitudes.estado_anterior',
                'solicitudes.procesando_desde',
                'solicitudes.certfdo_cita',
                'solicitudes.fecha_cita',
                'solicitudes.hora_cita',
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

        $bloqueadaDesde = 'COALESCE(solicitudes.procesando_desde, solicitudes.updated_at)';

        if ($dias = $this->option('dias')) {
            $query->whereRaw("{$bloqueadaDesde} < ?", [now()->subDays((int) $dias)]);
        }

        $minutos = $this->option('minutos');
        if ($minutos === null && $auto) {
            $minutos = self::MINUTOS_AUTO;
        }
        if ($minutos !== null) {
            $query->whereRaw("{$bloqueadaDesde} < ?", [now()->subMinutes((int) $minutos)]);
        }

        return $query->orderBy('solicitudes.id')->get();
    }

    /**
     * Separa las que sí llegaron a agendarse de las que nunca se agendaron.
     *
     * certfdo_cita solo se escribe en el mismo UPDATE que pone estado='Agendado'
     * dentro de cita(), así que su presencia indica que la cita se guardó.
     *
     * @return array{0: array, 1: array, 2: array}
     */
    private function clasificar($solicitudes): array
    {
        $notificadas = [];
        $sinAgendar  = [];
        $filas       = [];

        foreach ($solicitudes as $sol) {
            $certificado = $sol->certfdo_cita;
            $existeCert  = $certificado && file_exists(public_path($certificado));

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
                mb_strimwidth((string) $sol->servnomb, 0, 22, '...'),
                $sol->estado_anterior ?? '-',
                $existeCert ? 'si' : ($certificado ? 'ruta rota' : 'no'),
                $sol->fecha_cita ? trim($sol->fecha_cita.' '.$sol->hora_cita) : '-',
                $sol->procesando_desde ? (string) $sol->procesando_desde : $sol->updated_at.' (aprox.)',
                $clasificacion,
                $accion,
            ];
        }

        return [$notificadas, $sinAgendar, $filas];
    }

    /**
     * Aplica los cambios de estado.
     *
     * @return array{0: int, 1: int}
     */
    private function reparar(array $notificadas, array $sinAgendar): array
    {
        $agendadas = 0;
        $devueltas = 0;

        DB::transaction(function () use ($notificadas, $sinAgendar, &$agendadas, &$devueltas) {
            foreach ($notificadas as $sol) {
                solicitudes::where('id', $sol->id)->update([
                    'estado'           => 'Agendado',
                    'estado_anterior'  => null,
                    'procesando_desde' => null,
                ]);
                $agendadas++;
            }

            foreach ($sinAgendar as $item) {
                solicitudes::where('id', $item['sol']->id)->update([
                    'estado'           => $item['destino'],
                    'estado_anterior'  => null,
                    'procesando_desde' => null,
                ]);
                $devueltas++;
            }
        });

        return [$agendadas, $devueltas];
    }

    /**
     * Suelta los bloqueos de agendamiento vencidos.
     *
     * Desde que abrir el modal ya no cambia 'estado', un agendamiento
     * abandonado solo deja `procesando_desde` puesto. El bloqueo caduca por
     * tiempo, así que esto es únicamente higiene de datos: la solicitud sigue
     * en su cola y operativa aunque no se limpie.
     */
    private function limpiarBloqueosCaducados(): void
    {
        $liberados = solicitudes::whereNotNull('procesando_desde')
            ->where('procesando_desde', '<', now()->subMinutes(ConsultaGeneral::MINUTOS_BLOQUEO))
            ->update(['procesando_desde' => null]);

        if ($liberados > 0) {
            $this->info("Bloqueos de agendamiento caducados liberados: {$liberados}.");
        }
    }

    /** Watchdog: repara en silencio y deja constancia en el log. */
    private function ejecutarWatchdog(array $notificadas, array $sinAgendar): int
    {
        [$agendadas, $devueltas] = $this->reparar($notificadas, $sinAgendar);

        Log::warning('Watchdog liberó solicitudes atascadas en "Procesando"', [
            'agendadas' => $agendadas,
            'devueltas' => $devueltas,
            'ids'       => array_merge(
                array_map(function ($sol) { return $sol->id; }, $notificadas),
                array_map(function ($item) { return $item['sol']->id; }, $sinAgendar)
            ),
        ]);

        $this->info("Watchdog: {$agendadas} agendada(s), {$devueltas} devuelta(s) a la cola.");

        return self::SUCCESS;
    }

    /** Reenvía el certificado a los casos ya agendados. */
    private function reenviar(array $notificadas): void
    {
        $this->line('');
        $this->info('Reenviando certificado de citación...');

        $enviados = 0;
        $fallidos = 0;

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

    /**
     * Los correos de citación se envían en cola: si nadie está ejecutando
     * `queue:work`, los jobs se acumulan y el paciente nunca recibe la cita.
     */
    private function revisarCola(): void
    {
        if (config('queue.default') !== 'database') {
            return;
        }

        try {
            $pendientes = DB::table('jobs')
                ->where('created_at', '<', now()->subMinutes(10)->getTimestamp())
                ->count();
            $fallidos = DB::table('failed_jobs')->count();
        } catch (\Throwable $th) {
            return; // Sin tablas de cola no hay nada que revisar.
        }

        $this->info('COLA DE CORREOS');
        $this->line('  Jobs pendientes hace más de 10 min : '.$pendientes);
        $this->line('  Jobs fallidos                      : '.$fallidos);

        if ($pendientes > 0) {
            $this->error('  Hay correos encolados sin procesar: verifique que el worker esté');
            $this->error('  corriendo (php artisan queue:work).');
        }
        if ($fallidos > 0) {
            $this->warn('  Revise los fallidos con: php artisan queue:failed');
        }
        $this->line('');
    }
}
