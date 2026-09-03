<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Envía los correos de citación encolados. Con esto NO hace falta
        // ejecutar 'queue:work' a mano ni dejar un proceso vivo: basta con el
        // único cron de 'schedule:run' para que las citaciones salgan solas.
        //
        //   --stop-when-empty : termina en cuanto vacía la cola (no deja
        //                       procesos colgados en hosting compartido).
        //   --max-time=55     : nunca se solapa con el minuto siguiente.
        //   --timeout=45      : menor que retry_after (90) en config/queue.php,
        //                       si no la cola reintentaría y el paciente
        //                       recibiría la citación por duplicado.
        //   withoutOverlapping(2) : caducidad corta del bloqueo; con la de por
        //                       defecto (24 h) un proceso muerto dejaría la
        //                       cola parada un día entero.
        $schedule->command('queue:work --stop-when-empty --max-time=55 --tries=3 --timeout=45')
            ->everyMinute()
            ->withoutOverlapping(2)
            ->runInBackground();

        // Verifica integridad de solicitudes todos los días a las 2:00 AM
        $schedule->command('solicitudes:verificar-integridad')->dailyAt('02:00');

        // Watchdog: red de seguridad para solicitudes heredadas que quedaron en
        // 'Procesando' y limpieza de bloqueos de agendamiento caducados.
        $schedule->command('solicitudes:procesando --auto')
            ->everyFifteenMinutes()
            ->withoutOverlapping(20)
            ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
