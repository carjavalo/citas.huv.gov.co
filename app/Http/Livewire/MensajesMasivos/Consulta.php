<?php

namespace App\Http\Livewire\MensajesMasivos;

use App\Mail\MensajeMasivoCitacion;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class Consulta extends Component
{
    use WithFileUploads;

    /** Encabezados que se buscan en el Excel (clave interna => texto del encabezado). */
    const COLUMNAS = [
        'documento'    => 'NUMERO DE DOCUMENTO',
        'especialidad' => 'ESPECIALIDAD',
        'dia'          => 'DIA',
        'fecha'        => 'FECHA DE CITA',
        'especialista' => 'NOMBRE ESPECIALISTA',
        'hora'         => 'HORA DE CITA',
    ];

    const SEDES = [
        'cali'    => [
            'nombre'    => 'Sede Cali',
            'direccion' => 'SEDE PRINCIPAL DEL HOSPITAL calle 5 #36-08 san Fernando',
        ],
        'cartago' => [
            'nombre'    => 'Sede Cartago',
            'direccion' => 'SEDE CARTAGO carrera 3b # 1a - 163 Barrio collarejo, Cartago, Valle del Cauca',
        ],
    ];

    /** Máximo de registros por archivo: todo el listado viaja en cada petición de Livewire. */
    const MAX_REGISTROS = 5000;

    /** Filas que se leen del archivo (registros + margen para títulos y encabezado). */
    const MAX_FILAS_LECTURA = self::MAX_REGISTROS + 16;

    const POR_PAGINA = 100;

    const DIAS = ['DOMINGO', 'LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'];

    public $archivo;
    public $sede = 'cali';

    /** Filas leídas del Excel con su usuario asociado (si existe). */
    public $registros = [];
    public $columnasFaltantes = [];
    public $nombreArchivo;
    public $nombreHoja;

    public $filtro = 'todos';
    public $busqueda = '';
    public $pagina = 1;

    /** Índices de $registros marcados para envío. */
    public $seleccionados = [];

    public $vistaPrevia = null;
    public $modalConfirmar = false;
    public $resultadoEnvio = null;

    // 'txt' porque el servidor detecta los .csv como text/plain; la extensión
    // real se valida en procesarArchivo().
    protected $rules = [
        'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:2048',
        'sede'    => 'required|in:cali,cartago',
    ];

    protected $messages = [
        'archivo.required' => 'Debe seleccionar un archivo de Excel.',
        'archivo.mimes'    => 'El archivo debe ser de tipo .xlsx, .xls o .csv.',
        'archivo.max'      => 'El archivo no puede superar los 2 MB.',
    ];

    public function mount()
    {
        $this->autorizar();
    }

    /**
     * El middleware 'role' de la ruta no se vuelve a aplicar en las peticiones
     * de Livewire, así que se valida en cada una (hydrate corre antes de
     * cualquier acción o actualización de datos).
     */
    public function hydrate()
    {
        $this->autorizar();
    }

    public function procesarArchivo()
    {
        $this->validate();
        $this->reiniciarResultados();

        if (!in_array(strtolower($this->archivo->getClientOriginalExtension()), ['xlsx', 'xls', 'csv'])) {
            $this->addError('archivo', 'El archivo debe ser de tipo .xlsx, .xls o .csv.');
            return;
        }

        try {
            $filas = $this->leerExcel($this->archivo->getRealPath());
        } catch (\Throwable $th) {
            Log::error('Mensajes masivos: no se pudo leer el Excel', ['error' => $th->getMessage()]);
            $this->addError('archivo', 'No fue posible leer el archivo. Verifique que sea un Excel válido.');
            return;
        }

        if ($filas === null) {
            $this->addError('archivo', 'No se encontró la columna "NUMERO DE DOCUMENTO" en el archivo.');
            return;
        }

        if ($filas === false) {
            $this->addError('archivo', 'El archivo supera el máximo de ' . self::MAX_REGISTROS . ' registros por envío. Divídalo en varios archivos.');
            return;
        }

        if (empty($filas)) {
            $this->addError('archivo', 'El archivo no contiene registros con número de documento.');
            return;
        }

        $documentos = array_values(array_unique(array_column($filas, 'documento')));
        $usuarios = User::whereIn('ndocumento', $documentos)
            ->get(['id', 'name', 'apellido1', 'apellido2', 'ndocumento', 'email'])
            ->keyBy(function ($u) {
                return $this->normalizarDocumento($u->ndocumento);
            });

        foreach ($filas as $fila) {
            $usuario = $usuarios->get($fila['documento']);

            $fila['encontrado'] = (bool) $usuario;
            $fila['user_id']    = $usuario->id ?? null;
            $fila['nombre']     = $usuario ? $this->nombreCompleto($usuario) : null;
            $fila['email']      = $usuario->email ?? null;
            $fila['enviado']    = false;

            $this->registros[] = $fila;
        }

        $this->nombreArchivo = $this->archivo->getClientOriginalName();
        $this->seleccionados = array_map('strval', array_keys(array_filter($this->registros, function ($r) {
            return $this->puedeEnviarse($r);
        })));
    }

    public function limpiar()
    {
        $this->reset(['archivo', 'nombreArchivo', 'nombreHoja', 'filtro', 'busqueda']);
        $this->reiniciarResultados();
        $this->resetErrorBag();
    }

    public function updatingFiltro()
    {
        $this->pagina = 1;
    }

    public function updatingBusqueda()
    {
        $this->pagina = 1;
    }

    public function irAPagina($pagina)
    {
        $this->pagina = max(1, (int) $pagina);
    }

    public function seleccionarTodos()
    {
        $this->seleccionados = array_map('strval', array_keys(array_filter($this->registrosFiltrados(), function ($r) {
            return $this->puedeEnviarse($r);
        })));
    }

    public function deseleccionarTodos()
    {
        $this->seleccionados = [];
    }

    public function verMensaje($indice)
    {
        if (!isset($this->registros[$indice]) || !$this->registros[$indice]['encontrado']) {
            return;
        }

        $registro = $this->registros[$indice];
        $usuario = User::find($registro['user_id'], ['id', 'name', 'apellido1', 'apellido2', 'email']);
        if (!$usuario) {
            return;
        }

        $registro['nombre'] = $this->nombreCompleto($usuario);
        $this->vistaPrevia = [
            'nombre'  => $registro['nombre'],
            'email'   => $usuario->email,
            'mensaje' => $this->construirMensaje($registro),
        ];
    }

    public function cerrarVistaPrevia()
    {
        $this->vistaPrevia = null;
    }

    public function confirmarEnvio()
    {
        if (empty($this->indicesParaEnviar())) {
            $this->addError('envio', 'No hay registros seleccionados con correo electrónico para enviar.');
            return;
        }

        $this->resetErrorBag('envio');
        $this->modalConfirmar = true;
    }

    public function enviarMensajes()
    {
        $this->validateOnly('sede');
        $this->modalConfirmar = false;

        $indices = $this->indicesParaEnviar();

        // $registros viaja al navegador y puede volver alterado: el destinatario
        // y el nombre se toman otra vez de la tabla users.
        $usuarios = User::whereIn('id', array_filter(array_map(function ($i) {
                return $this->registros[$i]['user_id'];
            }, $indices)))
            ->get(['id', 'name', 'apellido1', 'apellido2', 'ndocumento', 'email'])
            ->keyBy('id');

        $enviados = 0;
        $fallidos = 0;

        // Pacientes a los que se les confirmó (o no) la cita, para mostrarlos en pantalla.
        $confirmados = [];
        $noEnviados = [];

        foreach ($indices as $indice) {
            $registro = $this->registros[$indice];
            $usuario = $usuarios->get($registro['user_id']);

            if ($registro['enviado']) {
                continue;
            }

            if (!$usuario || empty($usuario->email) || $this->normalizarDocumento($usuario->ndocumento) !== $registro['documento']) {
                $fallidos++;
                $noEnviados[] = $this->resumenPaciente($registro);
                continue;
            }

            $registro['nombre'] = $this->nombreCompleto($usuario);

            try {
                Mail::to($usuario->email)->queue(new MensajeMasivoCitacion($this->construirMensaje($registro)));
                $this->registros[$indice]['enviado'] = true;
                $enviados++;
                $confirmados[] = $this->resumenPaciente($registro);
            } catch (\Throwable $th) {
                Log::error('Mensajes masivos: no se pudo encolar el correo', [
                    'user_id' => $usuario->id,
                    'error'   => $th->getMessage(),
                ]);
                $fallidos++;
                $noEnviados[] = $this->resumenPaciente($registro);
            }
        }

        Log::info('Mensajes masivos de citación', [
            'usuario'  => Auth::id(),
            'archivo'  => $this->nombreArchivo,
            'sede'     => $this->sede,
            'enviados' => $enviados,
            'fallidos' => $fallidos,
        ]);

        if ($enviados > 0) {
            $this->despacharCorreosEnSegundoPlano();
        }

        $this->seleccionados = [];
        $this->resultadoEnvio = [
            'enviados'    => $enviados,
            'fallidos'    => $fallidos,
            'confirmados' => $confirmados,
            'noEnviados'  => $noEnviados,
        ];
    }

    public function render()
    {
        $total = count($this->registros);
        $encontrados = count(array_filter($this->registros, function ($r) {
            return $r['encontrado'];
        }));
        $sinCorreo = count(array_filter($this->registros, function ($r) {
            return $r['encontrado'] && empty($r['email']);
        }));
        $incompletos = count(array_filter($this->registros, function ($r) {
            return $r['encontrado'] && !$r['completo'];
        }));

        // Solo se pinta una página de la tabla; los índices originales se conservan.
        $filtrados = $this->registrosFiltrados();
        $totalPaginas = max(1, (int) ceil(count($filtrados) / self::POR_PAGINA));
        $this->pagina = min(max(1, (int) $this->pagina), $totalPaginas);

        return view('livewire.mensajes-masivos.consulta', [
            'filas'         => array_slice($filtrados, ($this->pagina - 1) * self::POR_PAGINA, self::POR_PAGINA, true),
            'totalFiltrados' => count($filtrados),
            'totalPaginas'  => $totalPaginas,
            'sedes'         => self::SEDES,
            'total'         => $total,
            'encontrados'   => $encontrados,
            'noEncontrados' => $total - $encontrados,
            'sinCorreo'     => $sinCorreo,
            'incompletos'   => $incompletos,
            'porEnviar'     => count($this->indicesParaEnviar()),
            'pacientesPorEnviar' => $this->modalConfirmar
                ? array_map(function ($i) {
                    return $this->resumenPaciente($this->registros[$i]);
                }, $this->indicesParaEnviar())
                : [],
        ]);
    }

    private function resumenPaciente(array $registro)
    {
        return [
            'nombre'       => $registro['nombre'] ?: $registro['documento'],
            'documento'    => $registro['documento'],
            'especialidad' => $registro['especialidad'],
            'fecha'        => trim($registro['dia'] . ', ' . $registro['fecha'], ', '),
            'hora'         => $registro['hora'],
        ];
    }

    private function autorizar(): void
    {
        abort_unless(Auth::check() && Auth::user()->hasRole('Super Admin'), 403);
    }

    /** Arma el texto de la citación con los datos de la fila y la sede elegida. */
    private function construirMensaje(array $registro)
    {
        $direccion = self::SEDES[$this->sede]['direccion'] ?? self::SEDES['cali']['direccion'];

        return "Buen día,\n"
            . "Cordial saludo, SR(A) {$registro['nombre']}\n"
            . "Se escribe del HOSPITAL UNIVERSITARIO DEL VALLE EVARISTO GARCIA con el fin de informar que la cita DE {$registro['especialidad']} "
            . "que estaba pendiente queda programada para el día {$registro['dia']}, {$registro['fecha']} con el Dr. {$registro['especialista']} "
            . "a las {$registro['hora']} {$direccion} traer autorizacion vigente, orden medica, historia clinica "
            . "llegar 40 minutos antes de la consulta para facturar.";
    }

    private function nombreCompleto($usuario)
    {
        return trim(preg_replace('/\s+/', ' ', $usuario->name . ' ' . $usuario->apellido1 . ' ' . $usuario->apellido2));
    }

    private function reiniciarResultados()
    {
        $this->registros = [];
        $this->columnasFaltantes = [];
        $this->nombreHoja = null;
        $this->pagina = 1;
        $this->seleccionados = [];
        $this->vistaPrevia = null;
        $this->modalConfirmar = false;
        $this->resultadoEnvio = null;
    }

    private function puedeEnviarse(array $registro)
    {
        return $registro['encontrado'] && $registro['completo'] && !empty($registro['email']) && !$registro['enviado'];
    }

    /** La selección llega del navegador: se descartan valores raros e índices repetidos. */
    private function indicesParaEnviar()
    {
        $indices = array_unique(array_map('intval', array_filter((array) $this->seleccionados, function ($v) {
            return is_int($v) || (is_string($v) && ctype_digit($v));
        })));

        return array_values(array_filter($indices, function ($i) {
            return isset($this->registros[$i]) && $this->puedeEnviarse($this->registros[$i]);
        }));
    }

    /** Registros visibles en la tabla según filtro y búsqueda, conservando su índice. */
    private function registrosFiltrados()
    {
        $busqueda = mb_strtolower(trim($this->busqueda));

        return array_filter($this->registros, function ($r) use ($busqueda) {
            if ($this->filtro === 'encontrados' && !$r['encontrado']) {
                return false;
            }
            if ($this->filtro === 'no_encontrados' && $r['encontrado']) {
                return false;
            }
            if ($busqueda === '') {
                return true;
            }

            return mb_strpos(mb_strtolower($r['documento'] . ' ' . $r['nombre'] . ' ' . $r['email'] . ' ' . $r['especialidad']), $busqueda) !== false;
        });
    }

    /**
     * Busca la columna de documento en la hoja activa y, si no está, en las
     * demás hojas. Devuelve null si ninguna la tiene, false si supera
     * MAX_REGISTROS, o la lista de filas con los campos de COLUMNAS.
     */
    private function leerExcel($ruta)
    {
        $lector = $this->crearLector($ruta);

        // Sin este límite un archivo grande agota la memoria antes de poder
        // contar sus filas. Se anota qué hojas tienen algo más allá del límite.
        $limite = new class(self::MAX_FILAS_LECTURA) implements IReadFilter {
            public $hojasExcedidas = [];
            private $maxFilas;

            public function __construct($maxFilas)
            {
                $this->maxFilas = $maxFilas;
            }

            public function readCell($columnAddress, $row, $worksheetName = '')
            {
                if ($row > $this->maxFilas) {
                    $this->hojasExcedidas[$worksheetName] = true;
                    return false;
                }
                return true;
            }
        };
        $lector->setReadFilter($limite);
        $libro = $lector->load($ruta);

        $hojas = [$libro->getActiveSheet()];
        foreach ($libro->getWorksheetIterator() as $otraHoja) {
            if ($otraHoja !== $hojas[0]) {
                $hojas[] = $otraHoja;
            }
        }

        $hoja = null;
        $filaEncabezado = null;
        $mapa = [];
        foreach ($hojas as $hoja) {
            $ultimaFila = $hoja->getHighestDataRow();
            $ultimaColumna = Coordinate::columnIndexFromString($hoja->getHighestDataColumn());

            // El encabezado puede no estar en la fila 1 (títulos, logos, etc.).
            for ($fila = 1; $fila <= min(15, $ultimaFila) && $filaEncabezado === null; $fila++) {
                $mapa = [];
                for ($col = 1; $col <= $ultimaColumna; $col++) {
                    $texto = $this->normalizarEncabezado($hoja->getCellByColumnAndRow($col, $fila)->getFormattedValue());
                    foreach (self::COLUMNAS as $clave => $encabezado) {
                        if (!isset($mapa[$clave]) && $texto === $this->normalizarEncabezado($encabezado)) {
                            $mapa[$clave] = $col;
                        }
                    }
                }
                if (isset($mapa['documento'])) {
                    $filaEncabezado = $fila;
                }
            }

            if ($filaEncabezado !== null) {
                break;
            }
        }

        if ($filaEncabezado === null) {
            return null;
        }

        // Más allá del límite el lector también reporta celdas con solo formato
        // (bordes, alto de fila, fórmulas vacías): se rechaza el archivo solo si
        // allí hay documentos de verdad.
        $excedida = $lector instanceof Csv
            ? !empty($limite->hojasExcedidas)
            : isset($limite->hojasExcedidas[$hoja->getTitle()]);
        if ($excedida && $this->hayDocumentosTrasElLimite($ruta, $hoja->getTitle(), $mapa['documento'])) {
            return false;
        }

        $this->columnasFaltantes = array_values(array_diff_key(self::COLUMNAS, $mapa));
        $this->nombreHoja = $libro->getSheetCount() > 1 ? $hoja->getTitle() : null;
        $combinadas = $this->celdasCombinadas($hoja, $mapa, $filaEncabezado + 1, $ultimaFila);

        $filas = [];
        for ($fila = $filaEncabezado + 1; $fila <= $ultimaFila; $fila++) {
            $documento = $this->normalizarDocumento($this->valorDocumento($this->celda($hoja, $combinadas, $mapa['documento'], $fila)));
            if ($documento === '') {
                continue;
            }

            if (count($filas) >= self::MAX_REGISTROS) {
                return false;
            }

            $registro = ['fila' => $fila, 'documento' => $documento];
            foreach (array_keys(self::COLUMNAS) as $clave) {
                if ($clave === 'documento') {
                    continue;
                }
                $registro[$clave] = isset($mapa[$clave])
                    ? $this->valorCelda($this->celda($hoja, $combinadas, $mapa[$clave], $fila), $clave)
                    : '';
            }
            $registro['completo'] = !in_array('', array_intersect_key($registro, array_flip(['especialidad', 'dia', 'fecha', 'especialista', 'hora'])), true);

            $filas[] = $registro;
        }

        return $filas;
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

    /** Segunda lectura liviana: solo la columna de documento de la hoja usada, después del límite. */
    private function hayDocumentosTrasElLimite($ruta, $titulo, $colDocumento)
    {
        $lector = $this->crearLector($ruta)->setReadDataOnly(true);
        if (!$lector instanceof Csv) {
            $lector->setLoadSheetsOnly([$titulo]);
        }

        $letra = Coordinate::stringFromColumnIndex($colDocumento);
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
            if ($this->normalizarDocumento($this->valorDocumento($hoja->getCell($coordenada))) !== '') {
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

    /** Valor ya calculado: para fórmulas se prefiere el que guardó Excel (respeta el idioma). */
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

    /** El documento se toma sin el formato de la celda (miles, decimales, notación científica). */
    private function valorDocumento($celda)
    {
        $valor = $this->valorCalculado($celda);

        return is_float($valor) ? sprintf('%.0f', $valor) : (string) $valor;
    }

    /** Fechas y horas de Excel se guardan como números: se convierten a texto legible. */
    private function valorCelda($celda, $clave)
    {
        $valor = $this->valorCalculado($celda);

        if (is_numeric($valor) && in_array($clave, ['dia', 'fecha', 'hora']) && ExcelDate::isDateTime($celda, $valor)) {
            $fecha = ExcelDate::excelToDateTimeObject($valor);
            if ($clave === 'dia') {
                return self::DIAS[(int) $fecha->format('w')];
            }
            return $clave === 'hora' ? $fecha->format('h:i A') : $fecha->format('d/m/Y');
        }

        if (!$celda->isFormula()) {
            try {
                $valor = $celda->getFormattedValue();
            } catch (\Throwable $th) {
                // Se conserva el valor sin formato.
            }
        }

        // Los saltos de línea dentro de la celda no deben llegar al correo.
        return trim(preg_replace('/\s+/u', ' ', (string) $valor));
    }

    private function normalizarEncabezado($texto)
    {
        $texto = preg_replace('/[\s\x{00A0}\x{2007}\x{202F}\x{200B}\x{FEFF}]+/u', ' ', (string) $texto);
        $texto = mb_strtoupper(trim($texto));
        $texto = strtr($texto, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ñ' => 'N']);
        $texto = str_replace(['.', '°', 'º'], '', $texto);

        return trim(preg_replace('/\s+/u', ' ', $texto));
    }

    /** Quita prefijos de tipo de documento, espacios, puntos y separadores para comparar contra users.ndocumento. */
    private function normalizarDocumento($documento)
    {
        $documento = preg_replace('/^\s*(C\.?\s?C|T\.?\s?I|C\.?\s?E|R\.?\s?C|NUIP)\.?[\s:#-]*(?=\d)/iu', '', (string) $documento);
        $documento = preg_replace('/[\s\x{00A0}\x{200B}\x{FEFF}]+/u', '', $documento);

        // Un CSV guarda el documento como se ve en Excel: "1.234.567,00" no debe quedar "123456700".
        $documento = preg_replace('/[.,]0{1,2}$/', '', $documento);

        // SQL compara sin distinguir mayúsculas; aquí debe hacerse igual.
        return mb_strtoupper(preg_replace('/[\.,\-]+/', '', $documento));
    }

    /**
     * Vacía la cola de correos justo DESPUÉS de responder al navegador, igual
     * que en Citas\ConsultaGeneral: así las citaciones salen aunque no exista
     * el cron de 'schedule:run'.
     */
    private function despacharCorreosEnSegundoPlano(): void
    {
        // Con 'sync' el correo ya se envió dentro de la petición.
        if (config('queue.default') !== 'database') {
            return;
        }

        app()->terminating(function () {
            try {
                Artisan::call('queue:work', [
                    '--stop-when-empty' => true,
                    '--max-time'        => 50,
                    '--tries'           => 3,
                    // Menor que retry_after (90) en config/queue.php: si no, la
                    // cola reintentaría y el paciente recibiría duplicados.
                    '--timeout'         => 45,
                ]);
            } catch (\Throwable $th) {
                // El cron o la siguiente petición recogerán lo que quede.
                Log::warning('No se pudo vaciar la cola tras la petición', [
                    'error' => $th->getMessage(),
                ]);
            }
        });
    }
}
