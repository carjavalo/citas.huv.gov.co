{{--
    Importar usuarios desde Excel. Se incrusta en la pantalla de Usuarios, así
    que todo el estilo va en una hoja propia con prefijo ".impu": no depende del
    build de Tailwind ni altera el resto de la página.
--}}
<div class="impu">
    <div wire:ignore>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
        <style>
            .impu { display: inline-block; vertical-align: middle; }
            .impu *, .impu *::before, .impu *::after { box-sizing: border-box; }

            /* --- Botón que abre el módulo --------------------------------- */
            .impu-abrir {
                display: inline-flex; align-items: center; gap: 6px;
                margin-top: .25rem; padding: .5rem .9rem;
                border: 0; border-radius: .5rem; cursor: pointer;
                font: 600 13px/1 'Nunito', system-ui, sans-serif; letter-spacing: .02em;
                color: #fff; background: linear-gradient(135deg, #2c4370, #1e3a8a);
                box-shadow: 0 2px 6px rgba(30, 58, 138, .35);
                transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            }
            .impu-abrir:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(30, 58, 138, .4); filter: brightness(1.08); }
            .impu-abrir .material-icons { font-size: 18px; }

            /* --- Ventana modal -------------------------------------------- */
            .impu-fondo {
                position: fixed; inset: 0; z-index: 60;
                display: flex; align-items: center; justify-content: center; padding: 16px;
                background: rgba(15, 23, 42, .62); backdrop-filter: blur(3px);
                animation: impu-aparece .18s ease-out;
                font-family: 'Nunito', system-ui, sans-serif; text-align: left;
            }
            @keyframes impu-aparece { from { opacity: 0 } to { opacity: 1 } }
            @keyframes impu-sube { from { opacity: 0; transform: translateY(14px) scale(.985) } to { opacity: 1; transform: none } }

            .impu-caja {
                display: flex; flex-direction: column;
                width: 100%; max-width: 1120px; max-height: calc(100vh - 32px);
                background: #f8fafc; border-radius: 16px; overflow: hidden;
                box-shadow: 0 25px 60px rgba(2, 6, 23, .45);
                animation: impu-sube .22s ease-out;
            }
            .impu-caja.impu-angosta { max-width: 760px; }

            .impu-cabecera {
                display: flex; align-items: center; gap: 14px;
                padding: 18px 22px; color: #fff;
                background: linear-gradient(120deg, #1e3a8a 0%, #2c4370 55%, #0f766e 140%);
            }
            .impu-cabecera .impu-icono {
                display: flex; align-items: center; justify-content: center;
                width: 44px; height: 44px; flex: 0 0 44px;
                background: rgba(255, 255, 255, .16); border-radius: 12px;
            }
            .impu-cabecera h2 { margin: 0; font-size: 1.15rem; font-weight: 700; }
            .impu-cabecera p { margin: 2px 0 0; font-size: .8rem; opacity: .85; }
            .impu-cabecera .impu-crecer { flex: 1 1 auto; min-width: 0; }
            .impu-cerrar {
                display: flex; align-items: center; justify-content: center;
                width: 34px; height: 34px; border: 0; border-radius: 9px; cursor: pointer;
                color: #fff; background: rgba(255, 255, 255, .14); transition: background .15s;
            }
            .impu-cerrar:hover { background: rgba(255, 255, 255, .28); }

            .impu-cuerpo { padding: 20px 22px; overflow-y: auto; flex: 1 1 auto; }
            .impu-pie {
                display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
                padding: 14px 22px; background: #fff; border-top: 1px solid #e2e8f0;
            }
            .impu-pie .impu-crecer { flex: 1 1 auto; font-size: .78rem; color: #64748b; }

            /* --- Pasos ---------------------------------------------------- */
            .impu-pasos { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }
            .impu-paso { display: flex; align-items: center; gap: 8px; font-size: .78rem; color: #94a3b8; font-weight: 600; }
            .impu-paso span.n {
                display: flex; align-items: center; justify-content: center;
                width: 24px; height: 24px; border-radius: 50%;
                background: #e2e8f0; color: #64748b; font-size: .72rem;
            }
            .impu-paso.activo { color: #1e3a8a; }
            .impu-paso.activo span.n { background: #1e3a8a; color: #fff; }
            .impu-paso.listo { color: #0f766e; }
            .impu-paso.listo span.n { background: #0f766e; color: #fff; }
            .impu-flecha { color: #cbd5e1; font-size: 16px; }

            /* --- Tarjetas ------------------------------------------------- */
            .impu-tarjeta { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; }
            .impu-tarjeta + .impu-tarjeta { margin-top: 14px; }

            .impu-cifras { display: grid; gap: 12px; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
            .impu-cifra {
                display: flex; align-items: center; gap: 11px; padding: 12px 14px;
                background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
                text-align: left; cursor: pointer; width: 100%;
                transition: border-color .15s, box-shadow .15s, transform .15s;
            }
            .impu-cifra:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(15, 23, 42, .09); }
            .impu-cifra.sel { border-color: #1e3a8a; box-shadow: 0 0 0 2px rgba(30, 58, 138, .16); }
            .impu-cifra.impu-inerte { cursor: default; }
            .impu-cifra.impu-inerte:hover { transform: none; box-shadow: none; }
            .impu-cifra .mk {
                display: flex; align-items: center; justify-content: center;
                width: 38px; height: 38px; flex: 0 0 38px; border-radius: 10px;
            }
            .impu-cifra b { display: block; font-size: 1.4rem; line-height: 1.1; color: #0f172a; }
            .impu-cifra small { display: block; font-size: .72rem; color: #64748b; font-weight: 600; }

            .impu-azul   { background: #dbeafe; color: #1d4ed8; }
            .impu-verde  { background: #d1fae5; color: #047857; }
            .impu-ambar  { background: #fef3c7; color: #b45309; }
            .impu-rojo   { background: #fee2e2; color: #b91c1c; }
            .impu-gris   { background: #e2e8f0; color: #475569; }
            .impu-violeta{ background: #ede9fe; color: #6d28d9; }

            /* --- Zona de archivo ------------------------------------------ */
            .impu-soltar {
                display: block; border: 2px dashed #bfdbfe; border-radius: 14px;
                background: linear-gradient(180deg, #f0f7ff, #fff); padding: 24px; text-align: center;
                transition: border-color .15s, background .15s;
            }
            .impu-soltar:hover { border-color: #1e3a8a; }
            .impu-soltar .material-icons { font-size: 44px; color: #1e3a8a; }
            .impu-soltar h3 { margin: 8px 0 2px; font-size: .95rem; color: #0f172a; font-weight: 700; }
            .impu-soltar p { margin: 0; font-size: .78rem; color: #64748b; }
            .impu-soltar input[type=file] { display: block; margin: 14px auto 0; max-width: 420px; font-size: .8rem; }

            /* --- Mapa de columnas ----------------------------------------- */
            .impu-mapa { display: grid; gap: 8px; grid-template-columns: repeat(auto-fit, minmax(235px, 1fr)); }
            .impu-par {
                display: flex; align-items: center; gap: 8px; padding: 7px 10px;
                background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 9px; font-size: .76rem;
            }
            .impu-par code {
                padding: 2px 7px; border-radius: 5px; font-family: ui-monospace, Consolas, monospace;
                font-size: .72rem; background: #fff; border: 1px solid #cbd5e1; color: #1e3a8a; white-space: nowrap;
            }
            .impu-par .material-icons { font-size: 15px; color: #94a3b8; }
            .impu-par i { font-style: normal; color: #475569; }

            /* --- Avisos --------------------------------------------------- */
            .impu-aviso {
                display: flex; gap: 10px; align-items: flex-start;
                padding: 12px 14px; border-radius: 11px; font-size: .8rem; line-height: 1.45;
                border: 1px solid transparent; margin-bottom: 14px;
            }
            .impu-aviso .material-icons { font-size: 19px; flex: 0 0 auto; }
            .impu-aviso p { margin: 0; }
            .impu-aviso strong { font-weight: 700; }
            .impu-info  { background: #eff6ff; border-color: #bfdbfe; color: #1e40af; }
            .impu-alerta{ background: #fffbeb; border-color: #fde68a; color: #92400e; }
            .impu-error { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
            .impu-exito { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }

            /* --- Progreso ------------------------------------------------- */
            .impu-barra { height: 12px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
            .impu-barra > div {
                height: 100%; border-radius: 99px;
                background: repeating-linear-gradient(115deg, #1e3a8a 0 12px, #2c6ed5 12px 24px);
                background-size: 48px 100%; animation: impu-avanza 1s linear infinite;
                transition: width .35s ease;
            }
            @keyframes impu-avanza { to { background-position: 48px 0 } }
            .impu-pulso { animation: impu-late 1.4s ease-in-out infinite; }
            @keyframes impu-late { 0%,100% { opacity: 1 } 50% { opacity: .45 } }
            .impu-gira { animation: impu-rota 1s linear infinite; }
            @keyframes impu-rota { to { transform: rotate(360deg) } }

            /* --- Tabla ---------------------------------------------------- */
            .impu-marco { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
            .impu-herramientas {
                display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
                padding: 11px 14px; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
            }
            .impu-buscar { position: relative; flex: 1 1 220px; max-width: 340px; }
            .impu-buscar .material-icons {
                position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
                font-size: 17px; color: #94a3b8; pointer-events: none;
            }
            .impu-buscar input {
                width: 100%; padding: 7px 10px 7px 31px; font-size: .8rem;
                border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; color: #0f172a;
            }
            .impu-buscar input:focus { outline: 2px solid #bfdbfe; outline-offset: 0; border-color: #1e3a8a; }

            .impu-desplaza { overflow-x: auto; max-height: 46vh; overflow-y: auto; }
            .impu-tabla { width: 100%; border-collapse: collapse; font-size: .78rem; }
            .impu-tabla th {
                position: sticky; top: 0; z-index: 1;
                padding: 9px 11px; text-align: left; white-space: nowrap;
                background: #eef2f7; color: #475569; font-size: .68rem;
                font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
                border-bottom: 1px solid #dbe2ea;
            }
            .impu-tabla td { padding: 9px 11px; border-bottom: 1px solid #eef2f7; color: #334155; vertical-align: top; }
            .impu-tabla tbody tr:hover td { background: #f8fbff; }
            .impu-tabla .impu-vacio { padding: 36px 14px; text-align: center; color: #94a3b8; }
            .impu-tabla .num { font-family: ui-monospace, Consolas, monospace; white-space: nowrap; }
            .impu-tabla .fuerte { font-weight: 700; color: #0f172a; }
            .impu-tabla .apagado { color: #94a3b8; }
            .impu-clic { cursor: pointer; }

            .impu-pastilla {
                display: inline-flex; align-items: center; gap: 4px;
                padding: 2px 9px; border-radius: 99px; font-size: .68rem; font-weight: 700; white-space: nowrap;
            }
            .impu-pastilla .material-icons { font-size: 13px; }

            .impu-paginas {
                display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
                gap: 8px; padding: 9px 14px; border-top: 1px solid #e2e8f0; background: #f8fafc;
                font-size: .76rem; color: #64748b;
            }

            /* --- Botones -------------------------------------------------- */
            .impu-b {
                display: inline-flex; align-items: center; gap: 6px;
                padding: .5rem .95rem; border-radius: .5rem; border: 1px solid transparent;
                font: 600 .8rem/1.2 'Nunito', system-ui, sans-serif; cursor: pointer;
                transition: filter .15s, box-shadow .15s, background .15s;
            }
            .impu-b .material-icons { font-size: 17px; }
            .impu-b:disabled { opacity: .5; cursor: not-allowed; }
            .impu-b:not(:disabled):hover { filter: brightness(1.06); }
            .impu-b-principal { background: #1e3a8a; color: #fff; box-shadow: 0 2px 6px rgba(30, 58, 138, .3); }
            .impu-b-exito { background: #047857; color: #fff; box-shadow: 0 2px 6px rgba(4, 120, 87, .3); }
            .impu-b-plano { background: #fff; color: #334155; border-color: #cbd5e1; }
            .impu-b-plano:not(:disabled):hover { background: #f1f5f9; }
            .impu-b-fantasma { background: transparent; color: #1e3a8a; border-color: #bfdbfe; }
            .impu-b-fantasma:not(:disabled):hover { background: #eff6ff; }
            .impu-b-chico { padding: .3rem .5rem; }

            @media (max-width: 640px) {
                .impu-cabecera p { display: none; }
                .impu-cuerpo { padding: 14px; }
                .impu-pie { padding: 12px 14px; }
            }
        </style>
    </div>

    {{-- ===================================================================
         Botón: abre el módulo de importación
    =================================================================== --}}
    <button type="button" wire:click="abrir" class="impu-abrir" title="Importar usuarios desde un archivo de Excel">
        <span class="material-icons">upload_file</span> Importar
    </button>

    {{-- ===================================================================
         Modal de importación
    =================================================================== --}}
    @if ($modal)
        @php
            $pendientes = $conteo[\App\Models\ImportacionUsuarioDetalle::NUEVO] ?? 0;
            $hechos     = $conteo[\App\Models\ImportacionUsuarioDetalle::IMPORTADO] ?? 0;
            $existentes = $conteo[\App\Models\ImportacionUsuarioDetalle::EXISTENTE] ?? 0;
            $omitidos   = $conteo[\App\Models\ImportacionUsuarioDetalle::OMITIDO] ?? 0;
            $aMedias    = $importacion && $importacion->estado === \App\Models\ImportacionUsuario::IMPORTANDO && !$importando;
            $objetivo   = max(1, (int) ($importacion->nuevos ?? 0));
            $avance     = $importacion ? min(100, (int) round((($importacion->nuevos - $pendientes) / $objetivo) * 100)) : 0;
        @endphp

        <div class="impu-fondo">
            <div class="impu-caja">
                <div class="impu-cabecera">
                    <div class="impu-icono"><span class="material-icons">group_add</span></div>
                    <div class="impu-crecer">
                        <h2>Importar usuarios</h2>
                        <p>
                            @if (!$importacion)
                                Cargue un archivo de Excel para crear usuarios con rol {{ \App\Http\Livewire\Usuarios\Importar::ROL }}.
                            @elseif ($finalizada)
                                Resultado de {{ $importacion->archivo }}
                            @else
                                {{ $importacion->archivo }}@if ($importacion->hoja) · hoja «{{ $importacion->hoja }}»@endif
                            @endif
                        </p>
                    </div>
                    <button type="button" wire:click="abrirInformes" class="impu-b impu-b-plano" title="Ver el historial de importaciones">
                        <span class="material-icons">assessment</span> Informes
                    </button>
                    @unless ($importando)
                        <button type="button" wire:click="cerrar" class="impu-cerrar" aria-label="Cerrar">
                            <span class="material-icons">close</span>
                        </button>
                    @endunless
                </div>

                <div class="impu-cuerpo">
                    {{-- Pasos --}}
                    <div class="impu-pasos">
                        <div class="impu-paso {{ $importacion ? 'listo' : 'activo' }}">
                            <span class="n">@if($importacion)<span class="material-icons" style="font-size:14px">check</span>@else 1 @endif</span> Archivo
                        </div>
                        <span class="material-icons impu-flecha">arrow_forward</span>
                        <div class="impu-paso {{ $finalizada || $importando ? 'listo' : ($importacion ? 'activo' : '') }}">
                            <span class="n">@if($finalizada || $importando)<span class="material-icons" style="font-size:14px">check</span>@else 2 @endif</span> Validación
                        </div>
                        <span class="material-icons impu-flecha">arrow_forward</span>
                        <div class="impu-paso {{ $finalizada ? 'listo' : ($importando ? 'activo' : '') }}">
                            <span class="n">@if($finalizada)<span class="material-icons" style="font-size:14px">check</span>@else 3 @endif</span> Importación
                        </div>
                    </div>

                    {{-- ------------------------------------------------------
                         Paso 1: selección del archivo
                    ------------------------------------------------------ --}}
                    @if (!$importacion)
                        <div class="impu-tarjeta">
                            <label class="impu-soltar">
                                <span class="material-icons">cloud_upload</span>
                                <h3>Seleccione el archivo de Excel</h3>
                                <p>Formatos .xlsx, .xls o .csv · hasta 12 MB · máximo {{ number_format($maxRegistros, 0, ',', '.') }} registros</p>
                                <input type="file" wire:model="archivo" accept=".xlsx,.xls,.csv">
                            </label>

                            <div wire:loading wire:target="archivo" class="impu-aviso impu-info" style="margin:14px 0 0">
                                <span class="material-icons impu-gira">autorenew</span>
                                <p>Cargando el archivo...</p>
                            </div>

                            @error('archivo')
                                <div class="impu-aviso impu-error" style="margin:14px 0 0">
                                    <span class="material-icons">error_outline</span>
                                    <p>{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <div class="impu-tarjeta">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
                                <span class="material-icons" style="color:#1e3a8a">table_chart</span>
                                <strong style="font-size:.86rem;color:#0f172a">Columnas del archivo y campo donde se guarda</strong>
                            </div>
                            <div class="impu-mapa">
                                @foreach ([
                                    'nombre'         => 'Nombres',
                                    'apellido_1'     => 'Primer apellido',
                                    'apellido_2'     => 'Segundo apellido',
                                    'identificacion' => 'Identificación y contraseña',
                                    'telefono_1'     => 'Teléfono 1',
                                    'telefono_2'     => 'Teléfono 2',
                                    'correo_2'       => 'Correo (usuario de ingreso)',
                                    'responsable'    => 'EPS (se busca por nombre)',
                                ] as $clave => $destino)
                                    <div class="impu-par">
                                        <code>{{ $columnas[$clave][0] }}</code>
                                        <span class="material-icons">arrow_forward</span>
                                        <i>{{ $destino }}</i>
                                    </div>
                                @endforeach
                            </div>
                            <p style="margin:12px 0 0;font-size:.76rem;color:#64748b;line-height:1.5">
                                Los encabezados se reconocen en mayúsculas o minúsculas, con o sin acentos y con espacios,
                                guiones o guiones bajos. Pueden estar en cualquier orden y no tienen que ir en la primera fila.
                                Cada usuario importado queda con rol <strong>{{ \App\Http\Livewire\Usuarios\Importar::ROL }}</strong>,
                                tipo de documento <strong>Cédula de ciudadanía</strong>, correo verificado e ingresa con
                                <strong>su correo y su número de identificación</strong>.
                            </p>
                            <p style="margin:10px 0 0;font-size:.76rem;color:#64748b;line-height:1.5">
                                <span class="material-icons" style="font-size:15px;vertical-align:-3px;color:#b45309">unsubscribe</span>
                                Las filas cuyo correo sea de relleno (<code>noreporta@…</code>, <code>notiene@…</code>,
                                <code>sincorreo@…</code>, <code>0@0.00</code>) <strong>no se importan</strong>: ese correo no es de
                                la persona y es la clave de ingreso. Quedan listadas en el informe con su teléfono para
                                conseguirles uno propio.
                            </p>
                            <button type="button" wire:click="descargarPlantilla" class="impu-b impu-b-fantasma" style="margin-top:12px">
                                <span class="material-icons">download</span> Descargar plantilla de ejemplo
                            </button>
                        </div>
                    @endif

                    {{-- ------------------------------------------------------
                         Importación a medias que se puede reanudar
                    ------------------------------------------------------ --}}
                    @if ($aMedias)
                        <div class="impu-aviso impu-alerta">
                            <span class="material-icons">play_circle</span>
                            <p>
                                <strong>Esta importación quedó a medias.</strong>
                                Faltan {{ number_format($pendientes, 0, ',', '.') }} registros por crear de
                                {{ number_format($importacion->nuevos, 0, ',', '.') }}. Puede reanudarla donde iba.
                            </p>
                        </div>
                    @endif

                    {{-- ------------------------------------------------------
                         Resumen (validación, progreso y resultado)
                    ------------------------------------------------------ --}}
                    @if ($importacion)
                        @if (count($faltantes))
                            <div class="impu-aviso impu-alerta">
                                <span class="material-icons">warning_amber</span>
                                <p>
                                    <strong>El archivo no trae todas las columnas esperadas.</strong>
                                    Faltan: {{ implode(', ', $faltantes) }}. Esos datos quedarán vacíos en los usuarios creados.
                                </p>
                            </div>
                        @endif

                        @if ($finalizada)
                            <div class="impu-aviso {{ $hechos ? 'impu-exito' : 'impu-alerta' }}">
                                <span class="material-icons">{{ $hechos ? 'task_alt' : 'info' }}</span>
                                <p>
                                    <strong>Importación finalizada.</strong>
                                    Se crearon {{ number_format($hechos, 0, ',', '.') }} usuarios de
                                    {{ number_format($importacion->total_filas, 0, ',', '.') }} filas leídas.
                                    @if ($existentes) {{ number_format($existentes, 0, ',', '.') }} ya existían en el sistema. @endif
                                    @if ($omitidos) {{ number_format($omitidos, 0, ',', '.') }} se omitieron. @endif
                                </p>
                            </div>
                        @endif

                        @if ($importando)
                            <div class="impu-tarjeta" style="margin-bottom:14px">
                                <div style="display:flex;align-items:center;gap:9px;margin-bottom:9px">
                                    <span class="material-icons impu-gira" style="color:#1e3a8a">autorenew</span>
                                    <strong style="font-size:.88rem;color:#0f172a">Creando usuarios...</strong>
                                    <span class="impu-crecer"></span>
                                    <strong style="font-size:1rem;color:#1e3a8a">{{ $avance }}%</strong>
                                </div>
                                <div class="impu-barra"><div style="width:{{ max(2, $avance) }}%"></div></div>
                                <p style="margin:9px 0 0;font-size:.77rem;color:#64748b">
                                    {{ number_format($hechos, 0, ',', '.') }} creados ·
                                    {{ number_format($pendientes, 0, ',', '.') }} por procesar.
                                    No cierre esta ventana: el proceso avanza por lotes de
                                    {{ \App\Http\Livewire\Usuarios\Importar::LOTE }} registros.
                                    Si el avance se queda quieto, use «Continuar» para seguir con el siguiente lote;
                                    la importación también se reanuda volviendo a abrir esta ventana.
                                </p>
                                <button type="button" wire:click="procesarLote" class="impu-b impu-b-fantasma impu-b-chico"
                                        style="margin-top:10px" wire:loading.attr="disabled" wire:target="procesarLote">
                                    <span class="material-icons">redo</span> Continuar
                                </button>
                            </div>
                        @endif

                        <div class="impu-cifras" style="margin-bottom:14px">
                            <button type="button" class="impu-cifra {{ $filtro === '' ? 'sel' : '' }}" wire:click="filtrar('')">
                                <span class="mk impu-gris"><span class="material-icons">list_alt</span></span>
                                <span><b>{{ number_format($importacion->total_filas, 0, ',', '.') }}</b><small>Filas leídas</small></span>
                            </button>
                            @if (!$finalizada)
                                <button type="button" class="impu-cifra {{ $filtro === 'nuevo' ? 'sel' : '' }}" wire:click="filtrar('nuevo')">
                                    <span class="mk impu-azul"><span class="material-icons">person_add</span></span>
                                    <span><b>{{ number_format($pendientes, 0, ',', '.') }}</b><small>Por importar</small></span>
                                </button>
                            @endif
                            @if ($finalizada || $hechos)
                                <button type="button" class="impu-cifra {{ $filtro === 'importado' ? 'sel' : '' }}" wire:click="filtrar('importado')">
                                    <span class="mk impu-verde"><span class="material-icons">how_to_reg</span></span>
                                    <span><b>{{ number_format($hechos, 0, ',', '.') }}</b><small>Importados</small></span>
                                </button>
                            @endif
                            <button type="button" class="impu-cifra {{ $filtro === 'existente' ? 'sel' : '' }}" wire:click="filtrar('existente')">
                                <span class="mk impu-ambar"><span class="material-icons">how_to_reg</span></span>
                                <span><b>{{ number_format($existentes, 0, ',', '.') }}</b><small>Ya existían</small></span>
                            </button>
                            <button type="button" class="impu-cifra {{ $filtro === 'omitido' ? 'sel' : '' }}" wire:click="filtrar('omitido')">
                                <span class="mk impu-rojo"><span class="material-icons">block</span></span>
                                <span><b>{{ number_format($omitidos, 0, ',', '.') }}</b><small>Omitidos</small></span>
                            </button>
                            @if ($sinEps)
                                <span class="impu-cifra impu-inerte">
                                    <span class="mk impu-violeta"><span class="material-icons">health_and_safety</span></span>
                                    <span><b>{{ number_format($sinEps, 0, ',', '.') }}</b><small>Creados sin EPS</small></span>
                                </span>
                            @endif
                        </div>

                        @if ($descartados['relleno']['filas'])
                            <div class="impu-aviso impu-alerta">
                                <span class="material-icons">unsubscribe</span>
                                <p>
                                    <strong>{{ number_format($descartados['relleno']['filas'], 0, ',', '.') }} filas no se importan porque su correo es de relleno.</strong>
                                    Son casillas llenadas con «no reporta», «no tiene» o similares cuando el paciente no dio correo.
                                    No pertenecen a nadie y el correo es la clave de ingreso, así que no se crea ninguna cuenta con ellos:
                                </p>
                            </div>
                            <div class="impu-mapa" style="margin:-6px 0 14px">
                                @foreach ($descartados['relleno']['correos'] as $relleno)
                                    <div class="impu-par">
                                        <span class="impu-pastilla impu-rojo">{{ number_format($relleno->total, 0, ',', '.') }}</span>
                                        <i style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $relleno->email }}</i>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($descartados['compartido']['filas'])
                            <div class="impu-aviso impu-info">
                                <span class="material-icons">group</span>
                                <p>
                                    <strong>{{ number_format($descartados['compartido']['filas'], 0, ',', '.') }} filas traen un correo real que ya usa otra persona del mismo archivo</strong>
                                    (normalmente el de un familiar o acompañante). Como el correo no se puede repetir, de cada grupo
                                    se registra la primera persona y las demás quedan fuera:
                                </p>
                            </div>
                            <div class="impu-mapa" style="margin:-6px 0 14px">
                                @foreach ($descartados['compartido']['correos'] as $compartido)
                                    <div class="impu-par">
                                        <span class="impu-pastilla impu-ambar">{{ number_format($compartido->total, 0, ',', '.') }}</span>
                                        <i style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $compartido->email }}</i>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($descartados['relleno']['filas'] || $descartados['compartido']['filas'])
                            <p style="margin:-6px 0 14px;font-size:.76rem;color:#64748b">
                                Para registrar a esas personas el archivo debe traerles un correo propio.
                                Filtre por «Omitidos» para verlas todas, con su teléfono, o descargue el informe en Excel.
                            </p>
                        @endif

                        @if ($sinEps)
                            <div class="impu-aviso impu-info">
                                <span class="material-icons">health_and_safety</span>
                                <p>
                                    En {{ number_format($sinEps, 0, ',', '.') }} registros la columna
                                    <code>{{ $columnas['responsable'][0] }}</code> no coincidió con ninguna EPS del sistema,
                                    así que esos usuarios quedaron con la EPS
                                    <strong>{{ \App\Support\CoincidenciaEps::SIN_INFORMACION }}</strong>.
                                    Filtre por «Importados» para identificarlos y corregir su EPS al editar el usuario.
                                </p>
                            </div>
                        @endif

                        {{-- Tabla de registros --}}
                        <div class="impu-marco">
                            <div class="impu-herramientas">
                                <div class="impu-buscar">
                                    <span class="material-icons">search</span>
                                    <input type="text" wire:model.debounce.400ms="busqueda" placeholder="Buscar por documento, nombre, correo o responsable">
                                </div>
                                <span class="impu-crecer" style="flex:1 1 auto"></span>
                                <span style="font-size:.76rem;color:#64748b">
                                    {{ number_format($vistaPrevia['total'], 0, ',', '.') }}
                                    {{ $vistaPrevia['total'] === 1 ? 'registro' : 'registros' }}
                                    @if ($filtro) · {{ $estados[$filtro] }} @endif
                                </span>
                            </div>

                            <div class="impu-desplaza">
                                <table class="impu-tabla">
                                    <thead>
                                        <tr>
                                            <th>Fila</th>
                                            <th>Resultado</th>
                                            <th>Identificación</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Teléfonos</th>
                                            <th>EPS</th>
                                            <th>Observación</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($vistaPrevia['items'] as $fila)
                                            <tr>
                                                <td class="num apagado">{{ $fila->fila }}</td>
                                                <td>@include('livewire.usuarios.importar-estado', ['estado' => $fila->estado])</td>
                                                <td class="num fuerte">{{ $fila->ndocumento ?: '—' }}</td>
                                                <td>{{ $fila->nombre_completo ?: '—' }}</td>
                                                <td>{{ $fila->email ?: '—' }}</td>
                                                <td class="num">
                                                    {{ $fila->telefono1 ?: '—' }}
                                                    @if ($fila->telefono2)<br><span class="apagado">{{ $fila->telefono2 }}</span>@endif
                                                </td>
                                                <td>
                                                    @if ($fila->eps_nombre)
                                                        {{ $fila->eps_nombre }}
                                                    @elseif ($fila->responsable)
                                                        <span class="impu-pastilla impu-violeta">sin coincidencia</span>
                                                        <br><span class="apagado">{{ $fila->responsable }}</span>
                                                    @else
                                                        <span class="apagado">—</span>
                                                    @endif
                                                </td>
                                                <td class="apagado">{{ $fila->motivo ?: '—' }}</td>
                                                <td class="num">@include('livewire.usuarios.importar-usuario', ['fila' => $fila])</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9" class="impu-vacio">No hay registros que coincidan con el filtro.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($vistaPrevia['totalPaginas'] > 1)
                                <div class="impu-paginas">
                                    <span>Página {{ $vistaPrevia['pagina'] }} de {{ $vistaPrevia['totalPaginas'] }}</span>
                                    <span style="display:flex;gap:6px">
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPagina({{ $vistaPrevia['pagina'] - 1 }})" @if($vistaPrevia['pagina'] <= 1) disabled @endif>
                                            <span class="material-icons">chevron_left</span>
                                        </button>
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPagina({{ $vistaPrevia['pagina'] + 1 }})" @if($vistaPrevia['pagina'] >= $vistaPrevia['totalPaginas']) disabled @endif>
                                            <span class="material-icons">chevron_right</span>
                                        </button>
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Pie de la ventana --}}
                <div class="impu-pie">
                    @if (!$importacion)
                        <span class="impu-crecer">Primero se analiza el archivo; nada se guarda hasta que usted lo confirme.</span>
                        <button type="button" wire:click="cerrar" class="impu-b impu-b-plano">Cancelar</button>
                        <button type="button" wire:click="analizar" class="impu-b impu-b-principal"
                                wire:loading.attr="disabled" wire:target="analizar,archivo">
                            <span class="material-icons" wire:loading.remove wire:target="analizar">fact_check</span>
                            <span class="material-icons impu-gira" wire:loading wire:target="analizar">autorenew</span>
                            Analizar archivo
                        </button>
                    @elseif ($importando)
                        <span class="impu-crecer impu-pulso">Importación en curso... la ventana se habilita al terminar.</span>
                    @elseif ($finalizada)
                        <span class="impu-crecer">Informe guardado. Puede consultarlo en cualquier momento desde «Informes».</span>
                        <button type="button" wire:click="exportarInforme({{ $importacion->id }})" class="impu-b impu-b-plano">
                            <span class="material-icons">download</span> Descargar informe
                        </button>
                        <button type="button" wire:click="descartar" class="impu-b impu-b-fantasma">
                            <span class="material-icons">restart_alt</span> Importar otro archivo
                        </button>
                        <button type="button" wire:click="cerrar" class="impu-b impu-b-principal">Finalizar</button>
                    @else
                        @php
                            $segundos = (int) ceil($pendientes * \App\Http\Livewire\Usuarios\Importar::MS_POR_REGISTRO / 1000);
                            $duracion = $segundos < 60
                                ? $segundos . ' segundos'
                                : 'unos ' . (int) ceil($segundos / 60) . ' minutos';
                        @endphp
                        <span class="impu-crecer">
                            @if ($pendientes)
                                Se crearán {{ number_format($pendientes, 0, ',', '.') }} usuarios nuevos. El resto no se toca.
                                @if ($segundos >= 30)
                                    <br><span class="material-icons" style="font-size:14px;vertical-align:-2px">schedule</span>
                                    Tardará {{ $duracion }}: deje esta ventana abierta.
                                @endif
                            @else
                                No hay registros nuevos por importar en este archivo.
                            @endif
                        </span>
                        <button type="button" wire:click="descartar" class="impu-b impu-b-plano">
                            <span class="material-icons">restart_alt</span> Cambiar archivo
                        </button>
                        <button type="button" wire:click="confirmar" class="impu-b impu-b-exito"
                                wire:loading.attr="disabled" wire:target="confirmar" @if(!$pendientes) disabled @endif>
                            <span class="material-icons">{{ $aMedias ? 'play_arrow' : 'cloud_done' }}</span>
                            {{ $aMedias ? 'Reanudar importación' : 'Importar ' . number_format($pendientes, 0, ',', '.') . ' registros' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ===================================================================
         Modal de informes
    =================================================================== --}}
    @if ($modalInformes)
        <div class="impu-fondo" style="z-index:70">
            <div class="impu-caja {{ $informe ? '' : 'impu-angosta' }}">
                <div class="impu-cabecera">
                    @if ($informe)
                        <button type="button" wire:click="volverAInformes" class="impu-cerrar" title="Volver al listado">
                            <span class="material-icons">arrow_back</span>
                        </button>
                    @else
                        <div class="impu-icono"><span class="material-icons">assessment</span></div>
                    @endif
                    <div class="impu-crecer">
                        <h2>{{ $informe ? 'Informe de importación #' . $informe->id : 'Informes de importación' }}</h2>
                        <p>
                            @if ($informe)
                                {{ $informe->archivo }} · {{ $informe->created_at->format('d/m/Y h:i A') }} · {{ $informe->autor_nombre }}
                            @else
                                Historial de los archivos importados, con sus totales y el detalle de cada registro.
                            @endif
                        </p>
                    </div>
                    @if ($informe)
                        <button type="button" wire:click="exportarInforme({{ $informe->id }})" class="impu-b impu-b-plano">
                            <span class="material-icons">download</span> Excel
                        </button>
                    @endif
                    <button type="button" wire:click="cerrarInformes" class="impu-cerrar" aria-label="Cerrar">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                <div class="impu-cuerpo">
                    {{-- --------------------------------------------------
                         Listado de informes
                    -------------------------------------------------- --}}
                    @unless ($informe)
                        <div class="impu-marco">
                            <div class="impu-desplaza" style="max-height:56vh">
                                <table class="impu-tabla">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Archivo</th>
                                            <th>Fecha</th>
                                            <th>Importó</th>
                                            <th style="text-align:right">Filas</th>
                                            <th style="text-align:right">Importados</th>
                                            <th style="text-align:right">Ya existían</th>
                                            <th style="text-align:right">Omitidos</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($informes['items'] as $fila)
                                            <tr class="impu-clic" wire:click="verInforme({{ $fila->id }})">
                                                <td class="num apagado">{{ $fila->id }}</td>
                                                <td class="fuerte">
                                                    {{ $fila->archivo }}
                                                    @if ($fila->estado === \App\Models\ImportacionUsuario::IMPORTANDO)
                                                        <br><span class="impu-pastilla impu-ambar"><span class="material-icons">schedule</span> sin terminar</span>
                                                    @endif
                                                </td>
                                                <td class="num">{{ $fila->created_at->format('d/m/Y h:i A') }}</td>
                                                <td>{{ $fila->autor_nombre }}</td>
                                                <td class="num" style="text-align:right">{{ number_format($fila->total_filas, 0, ',', '.') }}</td>
                                                <td class="num fuerte" style="text-align:right;color:#047857">{{ number_format($fila->importados, 0, ',', '.') }}</td>
                                                <td class="num" style="text-align:right;color:#b45309">{{ number_format($fila->existentes, 0, ',', '.') }}</td>
                                                <td class="num" style="text-align:right;color:#b91c1c">{{ number_format($fila->omitidos, 0, ',', '.') }}</td>
                                                <td><span class="material-icons apagado" style="font-size:18px">chevron_right</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="9" class="impu-vacio">Todavía no se ha importado ningún archivo.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($informes['totalPaginas'] > 1)
                                <div class="impu-paginas">
                                    <span>Página {{ $informes['pagina'] }} de {{ $informes['totalPaginas'] }} · {{ number_format($informes['total'], 0, ',', '.') }} informes</span>
                                    <span style="display:flex;gap:6px">
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPaginaInformes({{ $informes['pagina'] - 1 }})" @if($informes['pagina'] <= 1) disabled @endif>
                                            <span class="material-icons">chevron_left</span>
                                        </button>
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPaginaInformes({{ $informes['pagina'] + 1 }})" @if($informes['pagina'] >= $informes['totalPaginas']) disabled @endif>
                                            <span class="material-icons">chevron_right</span>
                                        </button>
                                    </span>
                                </div>
                            @endif
                        </div>
                        <p style="margin:12px 0 0;font-size:.76rem;color:#64748b">
                            Haga clic en un informe para ver, fila por fila, qué se importó y qué no.
                        </p>
                    @else
                        {{-- --------------------------------------------------
                             Detalle de un informe
                        -------------------------------------------------- --}}
                        @php
                            $dImportados = $conteoInforme[\App\Models\ImportacionUsuarioDetalle::IMPORTADO] ?? 0;
                            $dExistentes = $conteoInforme[\App\Models\ImportacionUsuarioDetalle::EXISTENTE] ?? 0;
                            $dOmitidos   = $conteoInforme[\App\Models\ImportacionUsuarioDetalle::OMITIDO] ?? 0;
                            $dPendientes = $conteoInforme[\App\Models\ImportacionUsuarioDetalle::NUEVO] ?? 0;
                        @endphp

                        <div class="impu-cifras" style="margin-bottom:14px">
                            <button type="button" class="impu-cifra {{ $filtroInforme === '' ? 'sel' : '' }}" wire:click="filtrarInforme('')">
                                <span class="mk impu-gris"><span class="material-icons">list_alt</span></span>
                                <span><b>{{ number_format($informe->total_filas, 0, ',', '.') }}</b><small>Filas leídas</small></span>
                            </button>
                            <button type="button" class="impu-cifra {{ $filtroInforme === 'importado' ? 'sel' : '' }}" wire:click="filtrarInforme('importado')">
                                <span class="mk impu-verde"><span class="material-icons">how_to_reg</span></span>
                                <span><b>{{ number_format($dImportados, 0, ',', '.') }}</b><small>Importados</small></span>
                            </button>
                            <button type="button" class="impu-cifra {{ $filtroInforme === 'existente' ? 'sel' : '' }}" wire:click="filtrarInforme('existente')">
                                <span class="mk impu-ambar"><span class="material-icons">person_search</span></span>
                                <span><b>{{ number_format($dExistentes, 0, ',', '.') }}</b><small>Ya existían</small></span>
                            </button>
                            <button type="button" class="impu-cifra {{ $filtroInforme === 'omitido' ? 'sel' : '' }}" wire:click="filtrarInforme('omitido')">
                                <span class="mk impu-rojo"><span class="material-icons">block</span></span>
                                <span><b>{{ number_format($dOmitidos, 0, ',', '.') }}</b><small>Omitidos</small></span>
                            </button>
                            @if ($dPendientes)
                                <button type="button" class="impu-cifra {{ $filtroInforme === 'nuevo' ? 'sel' : '' }}" wire:click="filtrarInforme('nuevo')">
                                    <span class="mk impu-azul"><span class="material-icons">schedule</span></span>
                                    <span><b>{{ number_format($dPendientes, 0, ',', '.') }}</b><small>Sin procesar</small></span>
                                </button>
                            @endif
                            @if ($informe->sin_eps)
                                <span class="impu-cifra impu-inerte">
                                    <span class="mk impu-violeta"><span class="material-icons">health_and_safety</span></span>
                                    <span><b>{{ number_format($informe->sin_eps, 0, ',', '.') }}</b><small>Creados sin EPS</small></span>
                                </span>
                            @endif
                        </div>

                        <div class="impu-marco">
                            <div class="impu-herramientas">
                                <div class="impu-buscar">
                                    <span class="material-icons">search</span>
                                    <input type="text" wire:model.debounce.400ms="busquedaInforme" placeholder="Buscar por documento, nombre, correo o responsable">
                                </div>
                                <span class="impu-crecer" style="flex:1 1 auto"></span>
                                <span style="font-size:.76rem;color:#64748b">
                                    {{ number_format($detalleInforme['total'], 0, ',', '.') }}
                                    {{ $detalleInforme['total'] === 1 ? 'registro' : 'registros' }}
                                    @if ($filtroInforme) · {{ $estados[$filtroInforme] }} @endif
                                </span>
                            </div>

                            <div class="impu-desplaza" style="max-height:44vh">
                                <table class="impu-tabla">
                                    <thead>
                                        <tr>
                                            <th>Fila</th>
                                            <th>Resultado</th>
                                            <th>Identificación</th>
                                            <th>Nombre</th>
                                            <th>Correo</th>
                                            <th>Teléfonos</th>
                                            <th>Responsable del archivo</th>
                                            <th>EPS asignada</th>
                                            <th>Observación</th>
                                            <th>Usuario</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($detalleInforme['items'] as $fila)
                                            <tr>
                                                <td class="num apagado">{{ $fila->fila }}</td>
                                                <td>@include('livewire.usuarios.importar-estado', ['estado' => $fila->estado])</td>
                                                <td class="num fuerte">{{ $fila->ndocumento ?: '—' }}</td>
                                                <td>{{ $fila->nombre_completo ?: '—' }}</td>
                                                <td>{{ $fila->email ?: '—' }}</td>
                                                <td class="num">
                                                    {{ $fila->telefono1 ?: '—' }}
                                                    @if ($fila->telefono2)<br><span class="apagado">{{ $fila->telefono2 }}</span>@endif
                                                </td>
                                                <td class="apagado">{{ $fila->responsable ?: '—' }}</td>
                                                <td>
                                                    @if ($fila->eps_nombre)
                                                        {{ $fila->eps_nombre }}
                                                    @elseif ($fila->responsable)
                                                        <span class="impu-pastilla impu-violeta">sin coincidencia</span>
                                                    @else
                                                        <span class="apagado">—</span>
                                                    @endif
                                                </td>
                                                <td class="apagado">{{ $fila->motivo ?: '—' }}</td>
                                                <td class="num">@include('livewire.usuarios.importar-usuario', ['fila' => $fila])</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="10" class="impu-vacio">No hay registros que coincidan con el filtro.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($detalleInforme['totalPaginas'] > 1)
                                <div class="impu-paginas">
                                    <span>Página {{ $detalleInforme['pagina'] }} de {{ $detalleInforme['totalPaginas'] }}</span>
                                    <span style="display:flex;gap:6px">
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPaginaInforme({{ $detalleInforme['pagina'] - 1 }})" @if($detalleInforme['pagina'] <= 1) disabled @endif>
                                            <span class="material-icons">chevron_left</span>
                                        </button>
                                        <button type="button" class="impu-b impu-b-plano impu-b-chico" wire:click="irAPaginaInforme({{ $detalleInforme['pagina'] + 1 }})" @if($detalleInforme['pagina'] >= $detalleInforme['totalPaginas']) disabled @endif>
                                            <span class="material-icons">chevron_right</span>
                                        </button>
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endunless
                </div>

                <div class="impu-pie">
                    <span class="impu-crecer">
                        @if ($informe)
                            Hoja «{{ $informe->hoja ?: 'única' }}» · {{ number_format($informe->nuevos, 0, ',', '.') }} registros detectados como nuevos al analizar.
                        @else
                            Cada informe guarda el detalle completo de su archivo.
                        @endif
                    </span>
                    <button type="button" wire:click="cerrarInformes" class="impu-b impu-b-plano">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
