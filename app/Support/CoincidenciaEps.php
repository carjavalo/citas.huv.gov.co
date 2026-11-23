<?php

namespace App\Support;

use App\Models\eps;

/**
 * Busca en la tabla eps la entidad que corresponde a la columna "responsable"
 * del archivo de importación.
 *
 * Los nombres guardados en eps vienen recortados a 30 caracteres en muchos
 * casos ("CAJA COMPENS FAMILIAR DEL ORIE"), así que no basta con comparar
 * textos iguales: se aceptan prefijos, contenciones y un parecido alto.
 */
class CoincidenciaEps
{
    /**
     * EPS que reciben los usuarios importados cuyo "responsable" no coincidió
     * con ninguna entidad. La crea la migración 2026_11_23_000002 y está
     * inactiva, así que no se ofrece en los selectores del sistema.
     */
    const SIN_INFORMACION = 'SIN INFORMACION';

    /** Parecido mínimo (en %) para dar por buena una coincidencia aproximada. */
    const SIMILITUD_MINIMA = 88;

    /** Longitud mínima del texto más corto para aceptar un prefijo. */
    const MINIMO_PREFIJO = 6;

    /** Longitud mínima del texto más corto para aceptar una contención. */
    const MINIMO_CONTENIDO = 8;

    /** Longitud mínima del núcleo para compararlo (evita casar siglas sueltas). */
    const MINIMO_NUCLEO = 4;

    /**
     * Palabras que no distinguen a una entidad de otra: "SURA", "E.P.S. SURA" y
     * "SURA EPS S.A." son la misma. Se quitan para comparar el núcleo del nombre.
     */
    const RUIDO = [
        'EPS', 'EPSS', 'ARS', 'IPS', 'ESS', 'ESE', 'ESP', 'SA', 'SAS', 'SAESP',
        'LTDA', 'LTD', 'SOCIEDAD', 'ENTIDAD', 'PROMOTORA', 'DE', 'DEL', 'LA', 'EL', 'Y',
    ];

    /** @var array<string, array{id: int, nombre: string, activa: bool}> nombre normalizado => eps */
    private $indice = [];

    /** @var array<string, string> nombre normalizado => núcleo, calculado una sola vez */
    private $nucleos = [];

    /** @var array<string, ?array> responsable normalizado => eps, para no repetir la comparación */
    private $memoria = [];

    public function __construct()
    {
        foreach (eps::orderBy('id')->get(['id', 'nombre', 'estado']) as $registro) {
            $clave = self::normalizar($registro->nombre);
            if ($clave === '') {
                continue;
            }

            // Si dos eps normalizan igual (duplicados del catálogo), gana la activa.
            if (!isset($this->indice[$clave]) || (!$this->indice[$clave]['activa'] && $registro->estado)) {
                $this->indice[$clave] = [
                    'id'     => (int) $registro->id,
                    'nombre' => $registro->nombre,
                    'activa' => (bool) $registro->estado,
                ];
                $this->nucleos[$clave] = self::nucleo($clave);
            }
        }
    }

    /**
     * @return array{id: int, nombre: string, activa: bool}|null
     */
    public function buscar($responsable)
    {
        $clave = self::normalizar($responsable);
        if ($clave === '') {
            return null;
        }

        if (array_key_exists($clave, $this->memoria)) {
            return $this->memoria[$clave];
        }

        return $this->memoria[$clave] = $this->comparar($clave);
    }

    /**
     * Id de la EPS "SIN INFORMACION". Se crea si falta (base restaurada sin la
     * migración) para que users.eps, que no admite nulos, siempre apunte a una
     * fila existente.
     */
    public static function idSinInformacion(): int
    {
        return (int) eps::firstOrCreate(
            ['nombre' => self::SIN_INFORMACION],
            ['estado' => 0]
        )->id;
    }

    /** Mayúsculas, sin acentos y con un solo espacio entre palabras. */
    public static function normalizar($texto)
    {
        $texto = LectorUsuariosExcel::normalizarEncabezado($texto);

        return trim(str_replace('_', ' ', $texto));
    }

    /**
     * Núcleo del nombre: lo que de verdad identifica a la entidad.
     * "E.P.S. SURA" y "SURA EPS S.A." dan los dos "SURA".
     */
    public static function nucleo($normalizado)
    {
        // "E P S SURA" (de "E.P.S. SURA") vuelve a ser "EPS SURA": si no, las
        // siglas sueltas no se reconocen como la palabra de ruido que son.
        $texto = preg_replace_callback('/\b(?:[A-Z0-9] )+[A-Z0-9]\b/', function ($coincide) {
            return str_replace(' ', '', $coincide[0]);
        }, $normalizado);

        $palabras = array_diff(array_filter(explode(' ', $texto)), self::RUIDO);

        return implode('', $palabras);
    }

    private function comparar($clave)
    {
        if (isset($this->indice[$clave])) {
            return $this->indice[$clave];
        }

        $nucleo = self::nucleo($clave);

        $mejor = null;
        $mejorPuntaje = 0.0;

        foreach ($this->indice as $nombre => $datos) {
            $puntaje = $this->puntuar($nombre, $clave, $nucleo);
            if ($puntaje <= 0) {
                continue;
            }

            // Entre dos candidatos igual de parecidos se prefiere la eps activa.
            if ($datos['activa']) {
                $puntaje += 0.25;
            }

            if ($puntaje > $mejorPuntaje) {
                $mejorPuntaje = $puntaje;
                $mejor = $datos;
            }
        }

        return $mejor;
    }

    /**
     * Qué tanto se parecen dos nombres. El mismo núcleo vale más que un
     * prefijo, un prefijo más que una contención y esta más que un parecido
     * suelto; entre dos del mismo tipo gana la coincidencia más larga.
     */
    private function puntuar($nombre, $clave, $nucleo)
    {
        if (mb_strlen($nucleo) >= self::MINIMO_NUCLEO && $nucleo === ($this->nucleos[$nombre] ?? null)) {
            return 250.0;
        }

        $corto = min(mb_strlen($nombre), mb_strlen($clave));

        if ($corto >= self::MINIMO_PREFIJO && (mb_strpos($nombre, $clave) === 0 || mb_strpos($clave, $nombre) === 0)) {
            return 200 + min($corto, 99) / 100;
        }

        if ($corto >= self::MINIMO_CONTENIDO && (mb_strpos($nombre, $clave) !== false || mb_strpos($clave, $nombre) !== false)) {
            return 100 + min($corto, 99) / 100;
        }

        similar_text($nombre, $clave, $porcentaje);

        return $porcentaje >= self::SIMILITUD_MINIMA ? $porcentaje : 0.0;
    }
}
