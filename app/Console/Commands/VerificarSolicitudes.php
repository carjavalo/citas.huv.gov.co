<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarSolicitudes extends Command
{
    protected $signature = 'solicitudes:verificar-integridad';
    protected $description = 'Verifica consecutivos y vinculación de documentos en solicitudes';

    public function handle()
    {
        $errores = [];
        // Verificar consecutivos
        $usuarios = DB::table('users')->pluck('id');
        foreach ($usuarios as $uid) {
            $sols = DB::table('solicitudes')->where('pacid', $uid)->orderBy('solnum')->pluck('solnum');
            $prev = 0;
            foreach ($sols as $solnum) {
                if ($solnum != $prev + 1) {
                    $errores[] = "Usuario $uid: consecutivo incorrecto en $solnum (esperado: " . ($prev + 1) . ")";
                }
                $prev = $solnum;
            }
        }
        // Verificar documentos
        $solicitudes = DB::table('solicitudes')->get();
        $docFields = ['certfdo_cita','pachis','pacdocid','pacauto','pacordmed'];
        foreach ($solicitudes as $sol) {
            foreach ($docFields as $field) {
                if (!empty($sol->$field)) {
                    $ruta = public_path($sol->$field);
                    if (!file_exists($ruta)) {
                        $errores[] = "Solicitud ID {$sol->id}: documento $field no encontrado en $ruta";
                    }
                }
            }
        }
        if (empty($errores)) {
            $this->info('No se detectaron errores de consecutivo ni de vinculación de documentos.');
        } else {
            foreach ($errores as $err) {
                $this->error($err);
            }
        }
    }
}
