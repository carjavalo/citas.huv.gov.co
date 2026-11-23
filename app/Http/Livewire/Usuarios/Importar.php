<?php

namespace App\Http\Livewire\Usuarios;

use App\Exports\ImportacionUsuariosExport;
use App\Exports\PlantillaUsuariosExport;
use App\Models\ImportacionUsuario;
use App\Models\ImportacionUsuarioDetalle;
use App\Models\User;
use App\Support\CoincidenciaEps;
use App\Support\CorreoDeRelleno;
use App\Support\LectorUsuariosExcel;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Importación de usuarios desde Excel (botón "Importar" de Usuarios → Consulta).
 *
 * Trabaja en dos pasos:
 *  1. Analizar: se lee el archivo y cada fila se guarda en
 *     importaciones_usuarios_detalle con lo que se hará con ella (nueva, ya
 *     existente u omitida). Nada se escribe todavía en la tabla users.
 *  2. Importar: los usuarios se crean por lotes sobre esas filas, de modo que
 *     la barra de progreso avance de verdad, la petición nunca se alargue y la
 *     importación pueda reanudarse si se cierra el navegador.
 *
 * El informe que queda es justamente ese detalle, así que lo que se ve en
 * pantalla y lo que se consulta después son los mismos datos.
 */
class Importar extends Component
{
    use WithFileUploads;

    /** Tipo de documento de todos los registros importados: Cédula de ciudadanía. */
    const TDOCUMENTO = 1;

    /** Rol de todos los registros importados. */
    const ROL = 'Usuario';

    /** Usuarios que se crean en cada petición (cada contraseña cuesta ~40 ms de bcrypt). */
    const LOTE = 100;

    /** Memoria (MB) que se pide para analizar el archivo, si el servidor da menos. */
    const MEMORIA_ANALISIS = 512;

    /**
     * Lo que tarda crear un usuario, medido: casi todo es el cifrado de la
     * contraseña. Sirve para avisar cuánto va a durar la importación.
     */
    const MS_POR_REGISTRO = 45;

    const POR_PAGINA = 25;

    const INFORMES_POR_PAGINA = 8;

    /** Un análisis sin confirmar se descarta pasado este tiempo. */
    const HORAS_ANALISIS = 24;

    /** emitSelf vuelve al servidor: así cada lote corre en su propia petición. */
    protected $listeners = ['importarSiguienteLote' => 'procesarLote'];

    // --- Modal de importación -------------------------------------------------
    public $modal = false;
    public $archivo;

    /** Cabecera en curso (analizada, importando o finalizada). */
    public $importacionId;
    public $nombreArchivo;
    public $nombreHoja;

    /** Columnas esperadas que el archivo no trae. */
    public $faltantes = [];

    public $importando = false;
    public $finalizada = false;

    // --- Tabla de la vista previa / del resultado -----------------------------
    public $filtro = '';
    public $busqueda = '';
    public $pagina = 1;

    // --- Modal de informes ----------------------------------------------------
    public $modalInformes = false;
    public $paginaInformes = 1;

    /** Informe abierto dentro del modal de informes. */
    public $informeId;
    public $filtroInforme = '';
    public $busquedaInforme = '';
    public $paginaInforme = 1;

    protected $rules = [
        // 'txt' porque el servidor detecta los .csv como text/plain; la
        // extensión real se valida en analizar().
        'archivo' => 'required|file|mimes:xlsx,xls,csv,txt|max:12288',
    ];

    protected $messages = [
        'archivo.required' => 'Debe seleccionar un archivo de Excel.',
        'archivo.mimes'    => 'El archivo debe ser de tipo .xlsx, .xls o .csv.',
        'archivo.max'      => 'El archivo no puede superar los 12 MB.',
    ];

    public function mount()
    {
        $this->autorizar();
    }

    /**
     * Los métodos públicos de Livewire se invocan desde el navegador y el
     * middleware de la ruta no se vuelve a aplicar, así que el permiso se
     * comprueba en cada petición (hydrate corre antes de cualquier acción).
     */
    public function hydrate()
    {
        $this->autorizar();
    }

    // =========================================================================
    // Modal de importación
    // =========================================================================

    public function abrir()
    {
        $this->reiniciar();

        // Una importación que quedó a medias (se cerró el navegador, se perdió
        // la conexión) se puede retomar donde iba.
        $aMedias = ImportacionUsuario::where('importado_por', Auth::id())
            ->where('estado', ImportacionUsuario::IMPORTANDO)
            ->latest('id')
            ->first();

        if ($aMedias) {
            $this->importacionId  = $aMedias->id;
            $this->nombreArchivo  = $aMedias->archivo;
            $this->nombreHoja     = $aMedias->hoja;
        }

        $this->modal = true;
    }

    public function cerrar()
    {
        // Mientras se están creando usuarios no se cierra: el proceso avanza
        // con las peticiones de este modal.
        if ($this->importando) {
            return;
        }

        $huboImportacion = $this->finalizada;

        $this->descartarAnalisisActual();
        $this->reiniciar();
        $this->modal = false;

        // La tabla de usuarios de la pantalla debe mostrar los nuevos registros.
        if ($huboImportacion) {
            $this->emitUp('usuariosImportados');
        }
    }

    public function analizar()
    {
        $this->validate();

        if (!in_array(strtolower($this->archivo->getClientOriginalExtension()), ['xlsx', 'xls', 'csv'], true)) {
            $this->addError('archivo', 'El archivo debe ser de tipo .xlsx, .xls o .csv.');
            return;
        }

        $nombreOriginal = $this->archivo->getClientOriginalName();
        $this->ampliarLimites();

        try {
            $lectura = (new LectorUsuariosExcel())->leer($this->archivo->getRealPath());
        } catch (\Throwable $th) {
            Log::error('Importar usuarios: no se pudo leer el Excel', ['error' => $th->getMessage()]);
            $this->addError('archivo', 'No fue posible leer el archivo. Verifique que sea un Excel válido.');
            return;
        }

        if (!$lectura['ok']) {
            $this->addError('archivo', $this->mensajeDeLectura($lectura['error']));
            return;
        }

        // Un análisis anterior sin confirmar (de este usuario, o viejo de otro)
        // no es un informe y no debe acumularse.
        $this->limpiarAnalisisAbandonados();
        $this->reiniciar();

        $autor = Auth::user();
        $importacion = ImportacionUsuario::create([
            'archivo'              => $this->recortar($nombreOriginal, 191),
            'hoja'                 => $this->recortar($lectura['hoja'], 191) ?: null,
            'importado_por'        => $autor->id,
            'importado_por_nombre' => $this->recortar(trim($autor->name . ' ' . $autor->apellido1), 191),
            'estado'               => ImportacionUsuario::ANALIZADO,
            'total_filas'          => count($lectura['filas']),
        ]);

        try {
            $importacion->update([
                'nuevos' => $this->guardarDetalle($importacion, $lectura['filas']),
            ]);
        } catch (\Throwable $th) {
            Log::error('Importar usuarios: no se pudo preparar el análisis', ['error' => $th->getMessage()]);
            $importacion->delete();
            $this->addError('archivo', 'No fue posible analizar el archivo. Intente de nuevo.');
            return;
        }

        $this->importacionId = $importacion->id;
        $this->nombreArchivo = $importacion->archivo;
        $this->nombreHoja    = $importacion->hoja;
        $this->faltantes     = $lectura['faltantes'];
        $this->reset('archivo');
    }

    /** Vuelve al paso de selección de archivo descartando el análisis actual. */
    public function descartar()
    {
        if ($this->importando) {
            return;
        }

        $this->descartarAnalisisActual();
        $this->reiniciar();
    }

    /** Arranca (o retoma) la creación de usuarios por lotes. */
    public function confirmar()
    {
        $importacion = $this->importacionActual();
        if (!$importacion || $importacion->estado === ImportacionUsuario::FINALIZADO) {
            return;
        }

        if (!$importacion->detalles()->where('estado', ImportacionUsuarioDetalle::NUEVO)->exists()) {
            $this->emit('alertError', 'Este archivo no tiene registros nuevos por importar.');
            return;
        }

        $importacion->update(['estado' => ImportacionUsuario::IMPORTANDO]);

        $this->importando = true;
        $this->finalizada = false;
        $this->filtro = '';
        $this->busqueda = '';
        $this->pagina = 1;

        $this->emitSelf('importarSiguienteLote');
    }

    /**
     * Crea los usuarios del siguiente lote. Al terminar vuelve a emitir el
     * evento para que el lote siguiente corra en una petición nueva.
     */
    public function procesarLote()
    {
        $importacion = $this->importacionActual();
        if (!$importacion || $importacion->estado !== ImportacionUsuario::IMPORTANDO) {
            $this->importando = false;
            return;
        }

        $this->importando = true;

        // El lote está dimensionado para terminar en pocos segundos; el margen
        // cubre un servidor lento sin dejar la petición abierta indefinidamente.
        @set_time_limit(180);

        $pendientes = $importacion->detalles()
            ->where('estado', ImportacionUsuarioDetalle::NUEVO)
            ->orderBy('id')
            ->limit(self::LOTE)
            ->get();

        // Se resuelve una vez por lote, no por registro.
        $epsSinInformacion = CoincidenciaEps::idSinInformacion();

        foreach ($pendientes as $detalle) {
            $this->crearUsuario($detalle, $epsSinInformacion);
        }

        if ($importacion->detalles()->where('estado', ImportacionUsuarioDetalle::NUEVO)->exists()) {
            $this->emitSelf('importarSiguienteLote');
            return;
        }

        $this->finalizar($importacion);
    }

    // =========================================================================
    // Filtros de la tabla
    // =========================================================================

    public function filtrar($estado)
    {
        $this->filtro = isset(ImportacionUsuarioDetalle::ESTADOS[$estado]) ? $estado : '';
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

    // =========================================================================
    // Modal de informes
    // =========================================================================

    public function abrirInformes()
    {
        $this->modalInformes = true;
        $this->informeId = null;
        $this->paginaInformes = 1;
        $this->limpiarFiltrosInforme();
    }

    public function cerrarInformes()
    {
        $this->modalInformes = false;
        $this->informeId = null;
    }

    public function verInforme($id)
    {
        $informe = ImportacionUsuario::informes()->find((int) $id);
        $this->informeId = $informe->id ?? null;
        $this->limpiarFiltrosInforme();
    }

    public function volverAInformes()
    {
        $this->informeId = null;
        $this->limpiarFiltrosInforme();
    }

    public function filtrarInforme($estado)
    {
        $this->filtroInforme = isset(ImportacionUsuarioDetalle::ESTADOS[$estado]) ? $estado : '';
        $this->paginaInforme = 1;
    }

    public function updatingBusquedaInforme()
    {
        $this->paginaInforme = 1;
    }

    public function irAPaginaInforme($pagina)
    {
        $this->paginaInforme = max(1, (int) $pagina);
    }

    public function irAPaginaInformes($pagina)
    {
        $this->paginaInformes = max(1, (int) $pagina);
    }

    /** Descarga el detalle completo de un informe en Excel. */
    public function exportarInforme($id)
    {
        $informe = ImportacionUsuario::informes()->find((int) $id);
        if (!$informe) {
            $this->emit('alertError', 'El informe ya no está disponible.');
            return;
        }

        return Excel::download(
            new ImportacionUsuariosExport($informe->id),
            'importacion_usuarios_' . $informe->id . '_' . $informe->created_at->format('Ymd_His') . '.xlsx'
        );
    }

    /** Archivo de ejemplo con los encabezados exactos que espera el módulo. */
    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaUsuariosExport(), 'plantilla_importar_usuarios.xlsx');
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render()
    {
        $importacion = $this->importacionActual();

        $vistaPrevia = ['items' => collect(), 'total' => 0, 'pagina' => 1, 'totalPaginas' => 1];
        $conteo = collect();
        $vacio = ['filas' => 0, 'correos' => collect()];
        $descartados = ['relleno' => $vacio, 'compartido' => $vacio];
        if ($importacion) {
            $conteo = $this->conteoPorEstado($importacion->id);
            $descartados = $this->correosDescartados($importacion->id);
            $vistaPrevia = $this->paginar(
                $this->consultaDetalle($importacion->id, $this->filtro, $this->busqueda),
                $this->pagina,
                self::POR_PAGINA
            );
            $this->pagina = $vistaPrevia['pagina'];
        }

        $informe = $this->informeId ? ImportacionUsuario::informes()->find($this->informeId) : null;
        $detalleInforme = ['items' => collect(), 'total' => 0, 'pagina' => 1, 'totalPaginas' => 1];
        $conteoInforme = collect();
        if ($informe) {
            $conteoInforme = $this->conteoPorEstado($informe->id);
            $detalleInforme = $this->paginar(
                $this->consultaDetalle($informe->id, $this->filtroInforme, $this->busquedaInforme),
                $this->paginaInforme,
                self::POR_PAGINA
            );
            $this->paginaInforme = $detalleInforme['pagina'];
        }

        $informes = ['items' => collect(), 'total' => 0, 'pagina' => 1, 'totalPaginas' => 1];
        if ($this->modalInformes && !$informe) {
            $informes = $this->paginar(
                ImportacionUsuario::informes()->orderByDesc('id'),
                $this->paginaInformes,
                self::INFORMES_POR_PAGINA
            );
            $this->paginaInformes = $informes['pagina'];
        }

        return view('livewire.usuarios.importar', [
            'importacion'    => $importacion,
            'conteo'         => $conteo,
            'porImportar'    => $conteo[ImportacionUsuarioDetalle::NUEVO] ?? 0,
            'vistaPrevia'    => $vistaPrevia,
            'descartados'    => $descartados,
            'sinEps'         => $importacion ? $this->sinEps($importacion->id) : 0,
            'informes'       => $informes,
            'informe'        => $informe,
            'conteoInforme'  => $conteoInforme,
            'detalleInforme' => $detalleInforme,
            'estados'        => ImportacionUsuarioDetalle::ESTADOS,
            'columnas'       => LectorUsuariosExcel::COLUMNAS,
            'maxRegistros'   => LectorUsuariosExcel::MAX_REGISTROS,
        ]);
    }

    // =========================================================================
    // Análisis
    // =========================================================================

    /**
     * Clasifica cada fila del archivo y la guarda como detalle del informe.
     * Aquí no se toca la tabla users: solo se decide qué pasará con cada fila.
     *
     * @return int filas que quedaron por crear
     */
    private function guardarDetalle(ImportacionUsuario $importacion, array $filas)
    {
        $eps = new CoincidenciaEps();
        $momento = now();

        [$documentosExistentes, $correosExistentes] = $this->registrosExistentes();

        // Repetidos dentro del mismo archivo: guardan la fila donde apareció el
        // dato por primera vez, para poder citarla en el motivo.
        $documentosVistos = [];
        $correosVistos = [];

        $nuevos = 0;
        $detalle = [];
        foreach ($filas as $fila) {
            $documento = $this->normalizarDocumento($fila['identificacion']);
            $correo = mb_strtolower(trim($fila['correo_2']));
            $coincidencia = $eps->buscar($fila['responsable']);

            [$estado, $motivo, $usuarioExistente] = $this->clasificar(
                $documento,
                $correo,
                $documentosExistentes,
                $correosExistentes,
                $documentosVistos,
                $correosVistos
            );

            if ($estado === ImportacionUsuarioDetalle::NUEVO) {
                $documentosVistos[$documento] = $fila['fila'];
                $correosVistos[$correo] = $fila['fila'];
                $nuevos++;
            }

            $detalle[] = [
                'importacion_id' => $importacion->id,
                'fila'           => $fila['fila'],
                // En las filas que ya existían apunta al usuario que las bloquea;
                // en las importadas, al usuario recién creado.
                'user_id'        => $usuarioExistente,
                'ndocumento'     => $this->recortar($documento, 50),
                'nombre'         => $this->recortar($fila['nombre'], 191),
                'apellido1'      => $this->recortar($fila['apellido_1'], 191),
                'apellido2'      => $this->recortar($fila['apellido_2'], 191),
                'email'          => $this->recortar($correo, 191),
                'telefono1'      => $this->recortar($this->normalizarTelefono($fila['telefono_1']), 30),
                'telefono2'      => $this->recortar($this->normalizarTelefono($fila['telefono_2']), 30),
                'responsable'    => $this->recortar($fila['responsable'], 191),
                'eps_id'         => $coincidencia['id'] ?? null,
                'eps_nombre'     => $this->recortar($coincidencia['nombre'] ?? null, 191),
                'estado'         => $estado,
                'motivo'         => $this->recortar($motivo, 191),
                'created_at'     => $momento,
            ];
        }

        foreach (array_chunk($detalle, 500) as $bloque) {
            ImportacionUsuarioDetalle::insert($bloque);
        }

        return $nuevos;
    }

    /**
     * Qué hacer con una fila, y por qué.
     *
     * El motivo distingue siempre entre lo que ya estaba EN EL SISTEMA (tabla
     * users) y lo que viene repetido EN EL ARCHIVO, y cita la prueba: el id del
     * usuario que bloquea la fila, o el número de fila donde ya venía el dato.
     * Sin ese detalle un "ya existe" no se puede comprobar y se presta a
     * confusión.
     *
     * @return array{0: string, 1: ?string, 2: ?int} estado, motivo, id del usuario que ya existe
     */
    private function clasificar($documento, $correo, array $documentos, array $correos, array $documentosVistos, array $correosVistos)
    {
        if ($documento === '') {
            return [ImportacionUsuarioDetalle::OMITIDO, 'La fila no trae número de identificación.', null];
        }

        if (mb_strlen($documento) > 50) {
            return [ImportacionUsuarioDetalle::OMITIDO, 'El número de identificación no es válido.', null];
        }

        if ($correo === '') {
            return [ImportacionUsuarioDetalle::OMITIDO, 'La fila no trae correo electrónico y el correo es la clave de ingreso.', null];
        }

        if (mb_strlen($correo) > 191 || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return [ImportacionUsuarioDetalle::OMITIDO, 'El correo electrónico no es válido.', null];
        }

        // Se comprueba antes que los duplicados: un correo de relleno no es de
        // nadie, así que no se crea la cuenta ni siquiera con la primera fila
        // que lo trae.
        if (CorreoDeRelleno::es($correo)) {
            return [
                ImportacionUsuarioDetalle::OMITIDO,
                'El correo es de relleno ("no reporta", "no tiene", "sin correo"...): no es de la persona y no sirve para ingresar.',
                null,
            ];
        }

        // El motivo se deja igual para todas las filas del mismo caso, para
        // poder agruparlas en Excel; la prueba concreta va en la columna
        // user_id, que señala al usuario que ya tenía el dato.
        if (isset($documentos[$documento])) {
            return [
                ImportacionUsuarioDetalle::EXISTENTE,
                'Esta identificación ya estaba en la tabla de usuarios ANTES de importar.',
                $documentos[$documento],
            ];
        }

        if (isset($correos[$correo])) {
            return [
                ImportacionUsuarioDetalle::EXISTENTE,
                'Este correo ya estaba en la tabla de usuarios ANTES de importar.',
                $correos[$correo],
            ];
        }

        if (isset($documentosVistos[$documento])) {
            return [
                ImportacionUsuarioDetalle::OMITIDO,
                'Identificación repetida DENTRO DEL ARCHIVO: ya venía en la fila ' . $documentosVistos[$documento]
                    . ', que sí se importó. No está duplicada en el sistema.',
                null,
            ];
        }

        if (isset($correosVistos[$correo])) {
            return [
                ImportacionUsuarioDetalle::OMITIDO,
                'Correo repetido DENTRO DEL ARCHIVO: ya venía en la fila ' . $correosVistos[$correo]
                    . '. Cada usuario necesita un correo distinto para poder ingresar.',
                null,
            ];
        }

        return [ImportacionUsuarioDetalle::NUEVO, null, null];
    }

    /**
     * Identificaciones y correos que ya están en users, con el id del usuario
     * que los tiene, para poder señalarlo en el informe.
     *
     * Se normalizan igual que el archivo por si en la tabla quedaron documentos
     * escritos con puntos, espacios o con el tipo de documento delante.
     *
     * @return array{0: array<string, int>, 1: array<string, int>}
     */
    private function registrosExistentes()
    {
        $documentos = [];
        $correos = [];

        // Query builder en vez de Eloquent: no hace falta hidratar 10.000 modelos.
        foreach (DB::table('users')->select('id', 'ndocumento', 'email')->cursor() as $usuario) {
            $documento = $this->normalizarDocumento($usuario->ndocumento);
            if ($documento !== '' && !isset($documentos[$documento])) {
                $documentos[$documento] = (int) $usuario->id;
            }
            $correo = mb_strtolower(trim((string) $usuario->email));
            if ($correo !== '' && !isset($correos[$correo])) {
                $correos[$correo] = (int) $usuario->id;
            }
        }

        return [$documentos, $correos];
    }

    // =========================================================================
    // Creación de usuarios
    // =========================================================================

    private function crearUsuario(ImportacionUsuarioDetalle $detalle, $epsSinInformacion)
    {
        try {
            DB::transaction(function () use ($detalle, $epsSinInformacion) {
                $usuario = new User();
                $usuario->forceFill([
                    'name'         => $this->recortar((string) $detalle->nombre, 191),
                    'apellido1'    => $this->recortar((string) $detalle->apellido1, 191),
                    'apellido2'    => $this->recortar((string) $detalle->apellido2, 191) ?: null,
                    'tdocumento'   => self::TDOCUMENTO,
                    'ndocumento'   => $detalle->ndocumento,
                    'telefono1'    => $this->recortar((string) $detalle->telefono1, 12),
                    'telefono2'    => $this->recortar((string) $detalle->telefono2, 191) ?: null,
                    // Si el responsable del archivo no coincidió con ninguna eps
                    // se apunta a "SIN INFORMACION": la columna no admite nulos
                    // y varias consultas cruzan users.eps con eps.id.
                    'eps'          => $detalle->eps_id ?: $epsSinInformacion,
                    'sede_id'      => null,
                    'pservicio_id' => null,
                    'email'        => $detalle->email,
                    // El usuario ingresa con su correo y su número de identificación.
                    'password'          => Hash::make($detalle->ndocumento),
                    'email_verified_at' => now(),
                ]);
                $usuario->save();

                // El modelo asigna "Usuario" al crearse; se confirma por si eso cambia.
                if ($usuario->roles->isEmpty()) {
                    $usuario->assignRole(self::ROL);
                }

                $detalle->update([
                    'user_id' => $usuario->id,
                    'estado'  => ImportacionUsuarioDetalle::IMPORTADO,
                    'motivo'  => null,
                ]);
            });
        } catch (QueryException $th) {
            // 1062: índice único de users. Alguien registró esa identificación o
            // ese correo entre el análisis y la importación.
            $duplicado = ((int) ($th->errorInfo[1] ?? 0)) === 1062;
            if (!$duplicado) {
                Log::error('Importar usuarios: no se pudo crear el usuario', [
                    'detalle' => $detalle->id,
                    'error'   => $th->getMessage(),
                ]);
            }

            $detalle->update([
                'estado' => $duplicado ? ImportacionUsuarioDetalle::EXISTENTE : ImportacionUsuarioDetalle::OMITIDO,
                'motivo' => $duplicado
                    ? 'La identificación o el correo ya estaban registrados.'
                    : 'No se pudo guardar el registro en la base de datos.',
            ]);
        } catch (\Throwable $th) {
            Log::error('Importar usuarios: no se pudo crear el usuario', [
                'detalle' => $detalle->id,
                'error'   => $th->getMessage(),
            ]);
            $detalle->update([
                'estado' => ImportacionUsuarioDetalle::OMITIDO,
                'motivo' => 'No se pudo guardar el registro.',
            ]);
        }
    }

    private function finalizar(ImportacionUsuario $importacion)
    {
        $conteo = $this->conteoPorEstado($importacion->id);

        $importacion->update([
            'estado'        => ImportacionUsuario::FINALIZADO,
            'importados'    => $conteo[ImportacionUsuarioDetalle::IMPORTADO] ?? 0,
            'existentes'    => $conteo[ImportacionUsuarioDetalle::EXISTENTE] ?? 0,
            'omitidos'      => $conteo[ImportacionUsuarioDetalle::OMITIDO] ?? 0,
            'sin_eps'       => $this->sinEps($importacion->id),
            'finalizado_at' => now(),
        ]);

        Log::info('Importación de usuarios', [
            'importacion' => $importacion->id,
            'usuario'     => Auth::id(),
            'archivo'     => $importacion->archivo,
            'importados'  => $importacion->importados,
            'existentes'  => $importacion->existentes,
            'omitidos'    => $importacion->omitidos,
        ]);

        $this->importando = false;
        $this->finalizada = true;
        // Se abre en los importados; si no se creó ninguno, en el listado completo.
        $this->filtro = $importacion->importados ? ImportacionUsuarioDetalle::IMPORTADO : '';
        $this->busqueda = '';
        $this->pagina = 1;
    }

    // =========================================================================
    // Consultas auxiliares
    // =========================================================================

    private function importacionActual()
    {
        return $this->importacionId ? ImportacionUsuario::find($this->importacionId) : null;
    }

    private function conteoPorEstado($importacionId)
    {
        return ImportacionUsuarioDetalle::where('importacion_id', $importacionId)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');
    }

    /**
     * Filas descartadas por el correo, en sus dos casos, que se explican
     * distinto y se corrigen distinto:
     *
     *  - relleno: el correo no es de nadie ("noreporta@...", "notiene@...").
     *    Ninguna fila con ese correo se importa.
     *  - compartido: un correo real que varias personas comparten (el de un
     *    familiar). Entra la primera y las demás quedan fuera, porque
     *    users.email es único.
     *
     * @return array{relleno: array, compartido: array}
     */
    private function correosDescartados($importacionId)
    {
        return [
            'relleno'    => $this->resumenDeMotivo($importacionId, 'El correo es de relleno%'),
            'compartido' => $this->resumenDeMotivo($importacionId, 'Correo repetido%'),
        ];
    }

    /** @return array{filas: int, correos: \Illuminate\Support\Collection} */
    private function resumenDeMotivo($importacionId, $motivo)
    {
        $base = ImportacionUsuarioDetalle::where('importacion_id', $importacionId)
            ->where('estado', ImportacionUsuarioDetalle::OMITIDO)
            ->where('motivo', 'like', $motivo);

        return [
            'filas'   => (clone $base)->count(),
            'correos' => (clone $base)->selectRaw('email, count(*) as total')
                ->groupBy('email')->orderByDesc('total')->limit(6)->get(),
        ];
    }

    /** Usuarios creados cuyo "responsable" no coincidió con ninguna eps. */
    private function sinEps($importacionId)
    {
        return ImportacionUsuarioDetalle::where('importacion_id', $importacionId)
            ->where('estado', ImportacionUsuarioDetalle::IMPORTADO)
            ->whereNull('eps_id')
            ->count();
    }

    private function consultaDetalle($importacionId, $estado, $busqueda)
    {
        $texto = trim((string) $busqueda);

        return ImportacionUsuarioDetalle::where('importacion_id', $importacionId)
            ->when(isset(ImportacionUsuarioDetalle::ESTADOS[$estado]), function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->when($texto !== '', function ($query) use ($texto) {
                $patron = '%' . $texto . '%';
                $query->where(function ($grupo) use ($patron) {
                    $grupo->where('ndocumento', 'like', $patron)
                        ->orWhere('email', 'like', $patron)
                        ->orWhere('nombre', 'like', $patron)
                        ->orWhere('apellido1', 'like', $patron)
                        ->orWhere('apellido2', 'like', $patron)
                        ->orWhere('responsable', 'like', $patron);
                });
            })
            ->orderBy('fila')
            ->orderBy('id');
    }

    /**
     * Paginación propia: el componente va dentro de la pantalla de usuarios,
     * que ya usa el parámetro "page" para su propia tabla.
     *
     * @return array{items: \Illuminate\Support\Collection, total: int, pagina: int, totalPaginas: int}
     */
    private function paginar($query, $pagina, $porPagina)
    {
        $total = (clone $query)->count();
        $totalPaginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min(max(1, (int) $pagina), $totalPaginas);

        return [
            'items'        => $query->forPage($pagina, $porPagina)->get(),
            'total'        => $total,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
        ];
    }

    // =========================================================================
    // Utilidades
    // =========================================================================

    private function reiniciar()
    {
        $this->reset([
            'archivo', 'importacionId', 'nombreArchivo', 'nombreHoja', 'faltantes',
            'importando', 'finalizada', 'filtro', 'busqueda', 'pagina',
        ]);
        $this->resetErrorBag();
    }

    /** Un análisis que no se confirmó no es un informe: se borra con su detalle. */
    private function descartarAnalisisActual()
    {
        $importacion = $this->importacionActual();
        if ($importacion && $importacion->estado === ImportacionUsuario::ANALIZADO) {
            $importacion->delete();
        }
    }

    /** Análisis de otras sesiones que nadie confirmó (se cerró el navegador). */
    private function limpiarAnalisisAbandonados()
    {
        ImportacionUsuario::where('estado', ImportacionUsuario::ANALIZADO)
            ->where(function ($query) {
                $query->where('importado_por', Auth::id())
                    ->orWhere('created_at', '<', now()->subHours(self::HORAS_ANALISIS));
            })
            ->delete();
    }

    private function mensajeDeLectura($error)
    {
        $encabezado = LectorUsuariosExcel::COLUMNAS[LectorUsuariosExcel::CLAVE][0];

        switch ($error) {
            case LectorUsuariosExcel::SIN_ENCABEZADO:
                return 'No se encontró la columna "' . $encabezado . '" en ninguna hoja del archivo.';
            case LectorUsuariosExcel::EXCEDE:
                return 'El archivo supera el máximo de ' . LectorUsuariosExcel::MAX_REGISTROS
                    . ' registros por importación. Divídalo en varios archivos.';
            default:
                return 'El archivo no contiene registros debajo de la fila de encabezados.';
        }
    }

    /**
     * Quita prefijos de tipo de documento, espacios y separadores de miles para
     * comparar contra users.ndocumento.
     */
    private function normalizarDocumento($documento)
    {
        $documento = preg_replace('/^\s*(C\.?\s?C|T\.?\s?I|C\.?\s?E|R\.?\s?C|NUIP|PA|PE|PT)\.?[\s:#-]*(?=\d)/iu', '', (string) $documento);
        $documento = preg_replace('/[\s\x{00A0}\x{200B}\x{FEFF}]+/u', '', $documento);

        // Un CSV guarda el documento como se ve en Excel: "1.234.567,00" no
        // debe quedar "123456700".
        $documento = preg_replace('/[.,]0{1,2}$/', '', $documento);

        return mb_strtoupper(preg_replace('/[\.,\-]+/', '', $documento));
    }

    /** El teléfono se guarda solo con dígitos (y el + de un indicativo). */
    private function normalizarTelefono($telefono)
    {
        $telefono = preg_replace('/[^0-9+]+/', '', (string) $telefono);

        return ltrim($telefono, '+') === '' ? '' : $telefono;
    }

    /**
     * Leer un archivo de hasta MAX_REGISTROS filas con muchas columnas puede
     * pedir unos cientos de MB. En un servidor con el límite por defecto el
     * análisis se cortaría a la mitad, así que se amplía solo para esta
     * petición, y solo si el servidor lo permite (en hosting compartido puede
     * estar bloqueado: entonces se trabaja con lo que haya).
     */
    private function ampliarLimites(): void
    {
        @set_time_limit(300);

        $actual = $this->bytes(ini_get('memory_limit'));
        if ($actual > 0 && $actual < self::MEMORIA_ANALISIS * 1024 * 1024) {
            @ini_set('memory_limit', self::MEMORIA_ANALISIS . 'M');
        }
    }

    /** Convierte "256M", "1G" o "-1" de php.ini a bytes. */
    private function bytes($valor)
    {
        $valor = trim((string) $valor);
        if ($valor === '' || (int) $valor < 0) {
            return 0; // sin límite
        }

        $numero = (int) $valor;
        switch (strtolower(substr($valor, -1))) {
            case 'g': return $numero * 1024 * 1024 * 1024;
            case 'm': return $numero * 1024 * 1024;
            case 'k': return $numero * 1024;
            default:  return $numero;
        }
    }

    /** Ajusta el texto al tamaño de la columna: MySQL en modo estricto rechaza valores más largos. */
    private function recortar($valor, $longitud)
    {
        return $valor === null ? null : mb_substr((string) $valor, 0, $longitud);
    }

    private function limpiarFiltrosInforme()
    {
        $this->filtroInforme = '';
        $this->busquedaInforme = '';
        $this->paginaInforme = 1;
    }

    /**
     * Quién importa usuarios se decide en el Gestor de permisos. Super Admin
     * siempre puede (Gate::before le concede todo).
     */
    private function autorizar(): void
    {
        abort_unless(Auth::check() && Auth::user()->can('admin.usuarios.importar'), 403);
    }
}
