<div class="font-body bg-gray-100 min-h-screen">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        .font-body { font-family: 'Inter', sans-serif; }
    </style>

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between">
            <div class="flex items-center text-sm">
                <span class="text-slate-500">Configuración</span>
                <span class="material-icons text-xs mx-2 text-slate-400">chevron_right</span>
                <span class="text-[#2c4370] font-medium">Mensajes Masivos</span>
            </div>
        </div>
    </div>

    {{-- Contenido Principal --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-[#2c4370]">Mensajes Masivos de Citación</h1>
            <p class="text-slate-500 mt-1">Importe un archivo de Excel, valide los pacientes registrados y envíeles por correo electrónico la programación de su cita.</p>
        </div>

        {{-- Paso 1: Importar archivo --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#2c4370] text-white text-sm font-semibold mr-3">1</span>
                <h2 class="text-lg font-semibold text-gray-800">Importar archivo y seleccionar sede</h2>
            </div>

            <form wire:submit.prevent="procesarArchivo" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Archivo de Excel (.xlsx, .xls, .csv) - máx. 2 MB</label>
                    <input type="file" wire:model="archivo" accept=".xlsx,.xls,.csv"
                           class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-[#2c4370] file:text-white hover:file:bg-[#1e3a8a]">
                    <div wire:loading wire:target="archivo" class="text-xs text-blue-600 mt-1">Cargando archivo...</div>
                    @error('archivo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sede de la cita</label>
                    <select wire:model="sede" class="block w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                        @foreach ($sedes as $clave => $datosSede)
                            <option value="{{ $clave }}">{{ $datosSede['nombre'] }}</option>
                        @endforeach
                    </select>
                    @error('sede') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button type="submit" wire:loading.attr="disabled" wire:target="procesarArchivo,archivo"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-[#1e3a8a] hover:bg-blue-800 text-white rounded-lg text-sm font-medium transition-colors shadow-md disabled:opacity-50">
                        <span wire:loading.remove wire:target="procesarArchivo" class="material-icons text-base mr-2">upload_file</span>
                        <svg wire:loading wire:target="procesarArchivo" class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        Procesar
                    </button>
                    @if (count($registros))
                        <button type="button" wire:click="limpiar" title="Limpiar"
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 rounded-lg text-sm">
                            <span class="material-icons text-base">restart_alt</span>
                        </button>
                    @endif
                </div>
            </form>

            <div class="mt-4 text-xs text-slate-500 bg-slate-50 border border-slate-200 rounded-lg p-3">
                <p class="font-medium text-slate-600 mb-1">
                    <span class="material-icons text-sm align-middle mr-1">info</span>
                    El archivo debe tener una fila de encabezados con estas columnas:
                </p>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach (\App\Http\Livewire\MensajesMasivos\Consulta::COLUMNAS as $encabezado)
                        <span class="px-2 py-0.5 bg-white border border-slate-300 rounded font-mono text-[11px] text-slate-700">{{ $encabezado }}</span>
                    @endforeach
                </div>
                <p class="mt-2">Dirección que se incluirá en el mensaje: <strong class="text-slate-700">{{ $sedes[$sede]['direccion'] ?? '' }}</strong></p>
            </div>
        </div>

        @if (count($columnasFaltantes))
            <div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 flex items-start">
                <span class="material-icons mr-2">warning</span>
                <div class="text-sm">
                    <p class="font-semibold">El archivo no tiene todas las columnas esperadas.</p>
                    <p>Faltan: <strong>{{ implode(', ', $columnasFaltantes) }}</strong>. Sin esos datos el mensaje quedaría incompleto, por lo que esas citaciones no se podrán enviar.</p>
                </div>
            </div>
        @endif

        @if ($resultadoEnvio)
            <div class="mb-6 rounded-xl p-4 flex items-start border {{ $resultadoEnvio['fallidos'] ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-green-50 border-green-200 text-green-800' }}">
                <span class="material-icons mr-2">{{ $resultadoEnvio['fallidos'] ? 'warning' : 'mark_email_read' }}</span>
                <div class="text-sm flex-1">
                    <p class="font-semibold">{{ $resultadoEnvio['enviados'] }} mensaje(s) quedaron en cola para envío por correo electrónico.</p>
                    @if ($resultadoEnvio['fallidos'])
                        <p>{{ $resultadoEnvio['fallidos'] }} mensaje(s) no se pudieron encolar. Revise el log del sistema.</p>
                    @endif

                    @if (count($resultadoEnvio['confirmados'] ?? []))
                        <p class="mt-3 font-semibold text-gray-700">Pacientes a los que se les confirmó la cita:</p>
                        @include('livewire.mensajes-masivos.lista-pacientes', ['pacientes' => $resultadoEnvio['confirmados']])
                    @endif

                    @if (count($resultadoEnvio['noEnviados'] ?? []))
                        <p class="mt-3 font-semibold text-red-700">Pacientes a los que no se les pudo enviar:</p>
                        @include('livewire.mensajes-masivos.lista-pacientes', ['pacientes' => $resultadoEnvio['noEnviados']])
                    @endif
                </div>
                <button type="button" wire:click="$set('resultadoEnvio', null)" title="Cerrar" class="ml-2 text-gray-400 hover:text-gray-600">
                    <span class="material-icons text-base">close</span>
                </button>
            </div>
        @endif

        @if (count($registros))
        {{-- La selección se sincroniza con el servidor solo al enviar, para no recargar la tabla en cada clic. --}}
        <div x-data="{ seleccion: @entangle('seleccionados').defer }">
            {{-- Resumen --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">Registros en archivo</p>
                    <p class="text-2xl font-bold text-[#2c4370]">{{ $total }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">Encontrados en el sistema</p>
                    <p class="text-2xl font-bold text-green-600">{{ $encontrados }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">No registrados</p>
                    <p class="text-2xl font-bold text-red-500">{{ $noEncontrados }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">Sin correo electrónico</p>
                    <p class="text-2xl font-bold text-amber-500">{{ $sinCorreo }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">Datos de cita incompletos</p>
                    <p class="text-2xl font-bold text-orange-500">{{ $incompletos }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <p class="text-xs text-slate-500">Seleccionados para envío</p>
                    <p class="text-2xl font-bold text-[#1e3a8a]" x-text="seleccion.length">{{ $porEnviar }}</p>
                </div>
            </div>

            {{-- Paso 2: Revisar y enviar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center mb-4">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#2c4370] text-white text-sm font-semibold mr-3">2</span>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Revisar y enviar citaciones</h2>
                            <p class="text-xs text-slate-500">Archivo: {{ $nombreArchivo }}@if ($nombreHoja) &middot; Hoja: <strong>{{ $nombreHoja }}</strong>@endif</p>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative">
                                <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">search</span>
                                <input type="text" wire:model.debounce.400ms="busqueda" placeholder="Buscar documento, nombre, correo..."
                                       class="pl-9 w-full sm:w-72 rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                            </div>
                            <select wire:model="filtro" class="rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                                <option value="todos">Todos</option>
                                <option value="encontrados">Encontrados</option>
                                <option value="no_encontrados">No registrados</option>
                            </select>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button type="button" wire:click="seleccionarTodos" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm">
                                <span class="material-icons text-base mr-1">done_all</span> Seleccionar todos
                            </button>
                            <button type="button" wire:click="deseleccionarTodos" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm">
                                <span class="material-icons text-base mr-1">remove_done</span> Quitar selección
                            </button>
                            <button type="button" wire:click="confirmarEnvio" :disabled="seleccion.length === 0"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="material-icons text-base mr-2">send</span> Enviar citaciones (<span x-text="seleccion.length">{{ $porEnviar }}</span>)
                            </button>
                        </div>
                    </div>
                    @error('envio') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <th class="px-4 py-3 w-10"></th>
                                <th class="px-4 py-3">Fila</th>
                                <th class="px-4 py-3">Documento</th>
                                <th class="px-4 py-3">Paciente</th>
                                <th class="px-4 py-3">Correo</th>
                                <th class="px-4 py-3">Especialidad</th>
                                <th class="px-4 py-3">Fecha y hora</th>
                                <th class="px-4 py-3">Especialista</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-center">Mensaje</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($filas as $indice => $registro)
                                <tr wire:key="registro-{{ $indice }}" class="{{ $registro['encontrado'] ? 'hover:bg-blue-50/40' : 'bg-red-50/40' }}">
                                    <td class="px-4 py-3">
                                        @if ($registro['encontrado'] && $registro['completo'] && $registro['email'] && !$registro['enviado'])
                                            {{-- Sin x-model: en Alpine 3.8 duplica el valor en filas que aparecen al paginar o filtrar. --}}
                                            <input type="checkbox" value="{{ $indice }}"
                                                   :checked="seleccion.includes('{{ $indice }}')"
                                                   @change="$event.target.checked ? (seleccion.includes('{{ $indice }}') || (seleccion = [...seleccion, '{{ $indice }}'])) : (seleccion = seleccion.filter(i => i !== '{{ $indice }}'))"
                                                   class="rounded border-gray-300 text-[#2c4370] focus:ring-[#2c4370]">
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $registro['fila'] }}</td>
                                    <td class="px-4 py-3 font-mono text-gray-800">{{ $registro['documento'] }}</td>
                                    <td class="px-4 py-3 text-gray-800">{{ $registro['nombre'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $registro['email'] ?? '—' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $registro['especialidad'] }}</td>
                                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                        {{ $registro['dia'] }} {{ $registro['fecha'] }}
                                        <span class="block text-xs text-gray-500">{{ $registro['hora'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $registro['especialista'] }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($registro['enviado'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <span class="material-icons text-xs mr-1">schedule_send</span> En cola
                                            </span>
                                        @elseif (!$registro['encontrado'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">No registrado</span>
                                        @elseif (!$registro['completo'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700" title="Faltan datos de la cita en el archivo">Datos incompletos</span>
                                        @elseif (!$registro['email'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Sin correo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Listo</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($registro['encontrado'])
                                            <button type="button" wire:click="verMensaje({{ $indice }})" title="Ver mensaje"
                                                    class="text-[#2c4370] hover:text-blue-800">
                                                <span class="material-icons">visibility</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-10 text-center text-gray-500">No hay registros que coincidan con el filtro.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="px-6 py-3 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-600">
                    <span>
                        Mostrando {{ $totalFiltrados ? (($pagina - 1) * \App\Http\Livewire\MensajesMasivos\Consulta::POR_PAGINA) + 1 : 0 }}
                        - {{ min($pagina * \App\Http\Livewire\MensajesMasivos\Consulta::POR_PAGINA, $totalFiltrados) }}
                        de {{ $totalFiltrados }} registros
                    </span>
                    @if ($totalPaginas > 1)
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="irAPagina({{ $pagina - 1 }})" @if ($pagina <= 1) disabled @endif
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                <span class="material-icons text-base">chevron_left</span>
                            </button>
                            <span>Página {{ $pagina }} de {{ $totalPaginas }}</span>
                            <button type="button" wire:click="irAPagina({{ $pagina + 1 }})" @if ($pagina >= $totalPaginas) disabled @endif
                                    class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                                <span class="material-icons text-base">chevron_right</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Modal: vista previa del mensaje --}}
    @if ($vistaPrevia)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="cerrarVistaPrevia">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-[#2c4370]">Vista previa del mensaje</h3>
                        <p class="text-xs text-slate-500">Para: {{ $vistaPrevia['nombre'] }} &lt;{{ $vistaPrevia['email'] ?: 'sin correo registrado' }}&gt;</p>
                    </div>
                    <button type="button" wire:click="cerrarVistaPrevia" class="text-gray-400 hover:text-gray-600">
                        <span class="material-icons">close</span>
                    </button>
                </div>
                <div class="px-6 py-5">
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-gray-800 whitespace-pre-line leading-relaxed">{{ $vistaPrevia['mensaje'] }}</div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button type="button" wire:click="cerrarVistaPrevia" class="px-4 py-2 bg-[#2c4370] hover:bg-[#1e3a8a] text-white rounded-lg text-sm">Cerrar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: confirmar envío --}}
    @if ($modalConfirmar)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl">
                <div class="px-6 py-5">
                    <div class="flex items-center mb-3">
                        <span class="material-icons text-green-600 mr-2">send</span>
                        <h3 class="text-lg font-semibold text-gray-800">Confirmar envío</h3>
                    </div>
                    <p class="text-sm text-gray-600">
                        Se enviará la citación por correo electrónico a <strong>{{ $porEnviar }}</strong> paciente(s) con la dirección de la
                        <strong>{{ $sedes[$sede]['nombre'] ?? '' }}</strong>:
                    </p>
                    <p class="text-xs text-gray-500 mt-2 bg-slate-50 border border-slate-200 rounded p-2">{{ $sedes[$sede]['direccion'] ?? '' }}</p>

                    <p class="text-sm font-semibold text-gray-700 mt-4">Pacientes a los que se les confirmará la cita:</p>
                    @include('livewire.mensajes-masivos.lista-pacientes', ['pacientes' => $pacientesPorEnviar])
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-2">
                    <button type="button" wire:click="$set('modalConfirmar', false)" class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm">Cancelar</button>
                    <button type="button" wire:click="enviarMensajes" wire:loading.attr="disabled" wire:target="enviarMensajes"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                        <span wire:loading.remove wire:target="enviarMensajes">Enviar</span>
                        <span wire:loading wire:target="enviarMensajes">Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
