<?php

use App\Support\CoincidenciaEps;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * EPS "SIN INFORMACION" para los usuarios importados cuya columna
 * "responsable" no coincidió con ninguna entidad del catálogo.
 *
 * users.eps no admite nulos y varias consultas del sistema cruzan
 * users.eps con eps.id mediante INNER JOIN (Citas\ConsultaGeneral,
 * DetalleCita...), así que un valor sin fila correspondiente dejaría las
 * solicitudes de esos pacientes fuera de los listados. Con esta fila el cruce
 * siempre encuentra la eps.
 *
 * Queda inactiva: no aparece en ningún selector (eps::activas()), pero sí en la
 * edición del usuario, para que se pueda corregir cuando se conozca su eps.
 */
class EpsSinInformacion extends Migration
{
    public function up()
    {
        if (!DB::table('eps')->where('nombre', CoincidenciaEps::SIN_INFORMACION)->exists()) {
            DB::table('eps')->insert([
                'nombre' => CoincidenciaEps::SIN_INFORMACION,
                'estado' => 0,
            ]);
        }
    }

    public function down()
    {
        // Solo se elimina si ningún usuario quedó apuntando a ella.
        $id = DB::table('eps')->where('nombre', CoincidenciaEps::SIN_INFORMACION)->value('id');
        if ($id && !DB::table('users')->where('eps', $id)->exists()) {
            DB::table('eps')->where('id', $id)->delete();
        }
    }
}
