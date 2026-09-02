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
        // Verifica integridad de solicitudes todos los días a las 2:00 AM
        $schedule->command('solicitudes:verificar-integridad')->dailyAt('02:00');

        // Watchdog: libera las solicitudes que llevan más de 30 minutos en
        // 'Procesando' ("En proceso"). Se quedaban bloqueadas para siempre
        // cuando el consultor abandonaba el modal de agendamiento.
        $schedule->command('solicitudes:procesando --auto')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
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
