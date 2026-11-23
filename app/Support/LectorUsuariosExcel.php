<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

/**
 * Lee el archivo de Excel de importación de usuarios y devuelve sus filas ya
 * normalizadas, sin saber nada de la base de datos.
 *
 * Busca la fila de encabezados (puede no ser la primera: títulos, logos...) en
 * la hoja activa y, si no la encuentra, en las demás hojas del libro.
 */
class LectorUsuariosExcel
{
    /**
     * Columnas del archivo. La primera variante es el encabezado que se
     * documenta en pantalla; las demás se aceptan como sinónimos.
     *
     * La comparación no distingue mayúsculas, acentos ni separadores, así que
     * "apellido_1", "APELLIDO 1", "Apellido-1" y "Apellido 1" son la misma
     * columna.
     */
    const COLUMNAS = [
        'nombre'         => ['NOMBRE', 'NOMBRES', 'PRIMER_NOMBRE', 'NAME'],
        'apellido_1'     => ['APELLIDO_1', 'APELLIDO1', 'PRIMER_APELLIDO', 'APELLIDO'],
        'apellido_2'     => ['APELLIDO_2', 'APELLIDO2', 'SEGUNDO_APELLIDO'],
        'identificacion' => ['IDENTIFICACION', 'NUMERO_DE_IDENTIFICACION', 'N_IDENTIFICACION', 'NUMERO_DE_DOCUMENTO', 'DOCUMENTO', 'NDOCUMENTO', 'CEDULA'],
        'telefono_1'     => ['TELEFONO_1', 'TELEFONO1', 'TELEFONO', 'CELULAR', 'CELULAR_1'],
        'telefono_2'     => ['TELEFONO_2', 'TELEFONO2', 'CELULAR_2'],
        'correo_2'       => ['CORREO_2', 'CORREO2', 'CORREO', 'CORREO_ELECTRONICO', 'EMAIL', 'E_MAIL'],
        'responsable'    => ['RESPONSABLE', 'EPS', 'ASEGURADORA', 'ENTIDAD_RESPONSABLE'],
    ];

    /** Columnas que se leen sin el formato de la celda (miles, decimales, notación científica). */
    const NUMERICAS = ['identificacion', 'telefono_1', 'telefono_2'];

    /** La fila de encabezados es la que trae esta columna. */
    const CLAVE = 'identificacion';

    /** Máximo de registros por archivo: la importación debe caber en una sesión de trabajo. */
    const MAX_REGISTROS = 10000;

    /** Filas que se leen del archivo (registros + margen para títulos y encabezado). */
    const MAX_FILAS_LECTURA = self::MAX_REGISTROS + 16;

    /** Hasta qué fila se busca el encabezado. */
    const MAX_FILAS_ENCABEZADO = 15;

    /** Encabezado no encontrado en ninguna hoja. */
    const SIN_ENCABEZADO = 'sin_encabezado';

    /** El archivo tiene más de MAX_REGISTROS filas con datos. */
    const EXCEDE = 'excede';

    /** No hay ninguna fila con datos debajo del encabezado. */
    const VACIO = 'vacio';

    /**
     * @return array{ok: bool, error: ?string, hoja: ?string, faltantes: list<string>, filas: list<array>}
     */
    public function leer($ruta)
    {
        // Pasada 1: solo las primeras filas, para ubicar la hoja, el encabezado
        // y en qué columna está cada campo. Es barata y evita cargar el archivo
        // entero antes de saber qué hace falta.
        $exploracion = $this->explorar($ruta);
        if ($exploracion === null) {
            return $this->resultado(self::SIN_ENCABEZADO);
        }

        $mapa = $exploracion['mapa'];
        $filaEncabezado = $exploracion['fila'];

        // Pasada 2: el contenido, leyendo ÚNICAMENTE las columnas que se usan.
        // Un listado de pacientes suele traer veinte o más columnas y solo
        // ocho interesan: sin este recorte la memoria se dispara con archivos
        // grandes.
        $lector = $this->crearLector($ruta);
        if (!$lector instanceof Csv) {
            $lector->setLoadSheetsOnly([$exploracion['titulo']]);
        }

        $limite = new class($exploracion['letras'], self::MAX_FILAS_LECTURA) implements IReadFilter {
            public $hojasExcedidas = [];
            private $letras;
            private $maxFilas;

            public function __construct(array $letras, $maxFilas)
            {
                $this->letras = $letras;
                $this->maxFilas = $maxFilas;
            }

            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                if (!in_array($columnAddress, $this->letras, true)) {
                    return false;
                }
                if ($row > $this->maxFilas) {
                    $this->hojasExcedidas[$worksheetName] = true;
                    return false;
                }
                return true;
            }
        };
        $lector->setReadFilter($limite);

        $libro = $lector->load($ruta);
        $hoja = $libro->getSheetByName($exploracion['titulo']) ?: $libro->getActiveSheet();

        // Más allá del límite el lector también reporta celdas con solo formato
        // (bordes, alto de fila, fórmulas vacías): se rechaza el archivo solo si
        // allí hay identificaciones de verdad.
        if (!empty($limite->hojasExcedidas)
            && $this->hayDatosTrasElLimite($ruta, $exploracion['titulo'], $mapa[self::CLAVE])) {
            return $this->resultado(self::EXCEDE);
        }

        $faltantes = [];
        foreach (self::COLUMNAS as $clave => $variantes) {
            if (!isset($mapa[$clave])) {
                $faltantes[] = $variantes[0];
            }
        }

        $ultimaFila = $hoja->getHighestDataRow();
        $combinadas = $this->celdasCombinadas($hoja, $mapa, $filaEncabezado + 1, $ultimaFila);

        $filas = [];
        for ($fila = $filaEncabezado + 1; $fila <= $ultimaFila; $fila++) {
            $registro = [];
            foreach (array_keys(self::COLUMNAS) as $clave) {
                $registro[$clave] = isset($mapa[$clave])
                    ? $this->valor($this->celda($hoja, $combinadas, $mapa[$clave], $fila), in_array($clave, self::NUMERICAS, true))
                    : '';
            }

            // Fila completamente vacía (bordes, totales, separadores): no es un registro.
            if (implode('', $registro) === '') {
                continue;
            }

            if (count($filas) >= self::MAX_REGISTROS) {
                return $this->resultado(self::EXCEDE);
            }

            $registro['fila'] = $fila;
            $filas[] = $registro;
        }

        if (empty($filas)) {
            return $this->resultado(self::VACIO);
        }

        return [
            'ok'        => true,
            'error'     => null,
            'hoja'      => $exploracion['varias'] ? $exploracion['titulo'] : null,
            'faltantes' => $faltantes,
            'filas'     => $filas,
        ];
    }

    /**
     * Pasada 1: busca la fila de encabezados en la hoja activa y, si no la
     * tiene, en las demás hojas. Solo carga las primeras filas, así que vale
     * igual para un archivo de cien filas que para uno de diez mil.
     *
     * @return array{titulo: string, varias: bool, fila: int, mapa: array<string, int>, letras: list<string>}|null
     */
    private function explorar($ruta)
    {
        $lector = $this->crearLector($ruta);
        $lector->setReadFilter(new class(self::MAX_FILAS_ENCABEZADO) implements IReadFilter {
            private $maxFilas;

            public function __construct($maxFilas)
            {
                $this->maxFilas = $maxFilas;
            }

            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                return $row <= $this->maxFilas;
            }
        });

        $libro = $lector->load($ruta);

        // La hoja activa primero; si no trae la columna clave, se buscan las demás.
        $hojas = [$libro->getActiveSheet()];
        foreach ($libro->getWorksheetIterator() as $otra) {
            if ($otra !== $hojas[0]) {
                $hojas[] = $otra;
            }
        }

        foreach ($hojas as $hoja) {
            [$fila, $mapa] = $this->ubicarEncabezado($hoja);
            if ($fila === null) {
                continue;
            }

            return [
                'titulo' => $hoja->getTitle(),
                'varias' => $libro->getSheetCount() > 1,
                'fila'   => $fila,
                'mapa'   => $mapa,
                'letras' => $this->letrasALeer($hoja, $mapa),
            ];
        }

        return null;
    }

    /**
     * Columnas que hay que cargar en la pasada 2: las de los campos y, además,
     * la primera columna de cada rango combinado que las cubra, porque ahí es
     * donde vive el valor. Los rangos combinados se conocen aunque la lectura
     * esté filtrada.
     *
     * @return list<string>
     */
    private function letrasALeer($hoja, array $mapa)
    {
        $columnas = array_values($mapa);

        foreach ($hoja->getMergeCells() as $rango) {
            [[$colInicio], [$colFin]] = Coordinate::rangeBoundaries($rango);
            foreach ($mapa as $col) {
                if ($col >= $colInicio && $col <= $colFin) {
                    $columnas[] = $colInicio;
                    break;
                }
            }
        }

        return array_values(array_unique(array_map(
            [Coordinate::class, 'stringFromColumnIndex'],
            $columnas
        )));
    }

    /**
     * Mayúsculas sin acentos y con cualquier separador reducido a "_", para que
     * el encabezado se reconozca tal como lo haya escrito el usuario.
     */
    public static function normalizarEncabezado($texto)
    {
        $texto = preg_replace('/[\x{00A0}\x{2007}\x{202F}\x{200B}\x{FEFF}]+/u', ' ', (string) $texto);
        $texto = mb_strtoupper(trim($texto), 'UTF-8');
        $texto = strtr($texto, [
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'Ñ' => 'N', 'Ç' => 'C',
        ]);

        return trim(preg_replace('/[^A-Z0-9]+/', '_', $texto), '_');
    }

    /**
     * Fila de encabezados y columna de cada campo. Las variantes se prueban en
     * orden, así "APELLIDO_1" gana sobre el sinónimo "APELLIDO" aunque esté más
     * a la derecha, y una columna ya tomada no se reutiliza para otro campo.
     *
     * @return array{0: ?int, 1: array<string, int>}
     */
    private function ubicarEncabezado($hoja)
    {
        $ultimaFila = $hoja->getHighestDataRow();
        $ultimaColumna = Coordinate::columnIndexFromString($hoja->getHighestDataColumn());

        for ($fila = 1; $fila <= min(self::MAX_FILAS_ENCABEZADO, $ultimaFila); $fila++) {
            $encabezados = [];
            for ($col = 1; $col <= $ultimaColumna; $col++) {
                $texto = self::normalizarEncabezado($hoja->getCellByColumnAndRow($col, $fila)->getFormattedValue());
                if ($texto !== '' && !isset($encabezados[$texto])) {
                    $encabezados[$texto] = $col;
                }
            }

            $mapa = [];
            foreach (self::COLUMNAS as $clave => $variantes) {
                foreach ($variantes as $variante) {
                    if (isset($encabezados[$variante]) && !in_array($encabezados[$variante], $mapa, true)) {
                        $mapa[$clave] = $encabezados[$variante];
                        break;
                    }
                }
            }

            if (isset($mapa[self::CLAVE])) {
                return [$fila, $mapa];
            }
        }

        return [null, []];
    }

    private function resultado($error)
    {
        return ['ok' => false, 'error' => $error, 'hoja' => null, 'faltantes' => [], 'filas' => []];
    }

    private function crearLector($ruta)
    {
        $lector = IOFactory::createReaderForFile($ruta);
        if ($lector instanceof Csv) {
            // Excel en español guarda los CSV en Windows-1252, no en UTF-8.
            $lector->setInputEncoding(Csv::GUESS_ENCODING)->setFallbackEncoding('Windows-1252');
        }
        $lector->setReadEmptyCells(false);

        return $lector;
    }

    /** Segunda lectura liviana: solo la columna clave de la hoja usada, después del límite. */
    private function hayDatosTrasElLimite($ruta, $titulo, $colClave)
    {
        $lector = $this->crearLector($ruta)->setReadDataOnly(true);
        if (!$lector instanceof Csv) {
            $lector->setLoadSheetsOnly([$titulo]);
        }

        $letra = Coordinate::stringFromColumnIndex($colClave);
        $lector->setReadFilter(new class($letra, self::MAX_FILAS_LECTURA) implements IReadFilter {
            private $letra;
            private $maxFilas;

            public function __construct($letra, $maxFilas)
            {
                $this->letra = $letra;
                $this->maxFilas = $maxFilas;
            }

            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                return $row > $this->maxFilas && $columnAddress === $this->letra;
            }
        });

        $hoja = $lector->load($ruta)->getActiveSheet();
        foreach ($hoja->getCoordinates() as $coordenada) {
            if ($this->valor($hoja->getCell($coordenada), true) !== '') {
                return true;
            }
        }

        return false;
    }

    /**
     * En celdas combinadas el valor solo existe en la primera celda del rango.
     * Se arma una sola vez el mapa "columna:fila" => celda de origen, solo para
     * las columnas leídas (Cell::getMergeRange recorre todos los rangos en cada
     * llamada y vuelve lenta la lectura de archivos grandes).
     */
    private function celdasCombinadas($hoja, array $mapa, $primeraFila, $ultimaFila)
    {
        $combinadas = [];
        foreach ($hoja->getMergeCells() as $rango) {
            [[$colInicio, $filaInicio], [$colFin, $filaFin]] = Coordinate::rangeBoundaries($rango);
            $origen = Coordinate::stringFromColumnIndex($colInicio) . $filaInicio;

            foreach ($mapa as $col) {
                if ($col < $colInicio || $col > $colFin) {
                    continue;
                }
                for ($fila = max($filaInicio, $primeraFila); $fila <= min($filaFin, $ultimaFila); $fila++) {
                    $combinadas["$col:$fila"] = $origen;
                }
            }
        }

        return $combinadas;
    }

    private function celda($hoja, array $combinadas, $col, $fila)
    {
        return isset($combinadas["$col:$fila"])
            ? $hoja->getCell($combinadas["$col:$fila"])
            : $hoja->getCellByColumnAndRow($col, $fila);
    }

    /**
     * Texto de la celda. Las columnas numéricas (identificación, teléfonos) se
     * leen sin el formato: Excel las guarda como número y las mostraría como
     * "1,23457E+09" o "1.234.567".
     */
    private function valor($celda, $numerica)
    {
        $valor = $this->valorCalculado($celda);

        if ($numerica) {
            if (is_float($valor) || is_int($valor)) {
                return sprintf('%.0f', $valor);
            }

            return trim(preg_replace('/\s+/u', ' ', (string) $valor));
        }

        if (!$celda->isFormula()) {
            try {
                $valor = $celda->getFormattedValue();
            } catch (\Throwable $th) {
                // Se conserva el valor sin formato.
            }
        }

        return trim(preg_replace('/\s+/u', ' ', (string) $valor));
    }

    /** Valor ya calculado: para fórmulas se prefiere el que guardó Excel. */
    private function valorCalculado($celda)
    {
        if (!$celda->isFormula()) {
            return $celda->getValue();
        }

        try {
            return $celda->getOldCalculatedValue() ?? $celda->getCalculatedValue();
        } catch (\Throwable $th) {
            return '';
        }
    }
}
