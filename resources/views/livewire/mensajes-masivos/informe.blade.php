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
                <a href="{{ route('configuracion.mensajes-masivos') }}" class="text-slate-500 hover:text-[#2c4370]">Mensajes Masivos</a>
                <span class="material-icons text-xs mx-2 text-slate-400">chevron_right</span>
                <span class="text-[#2c4370] font-medium">Informe de confirmaciones</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#2c4370]">Informe de confirmaciones de citas</h1>
                <p class="text-slate-500 mt-1">Citaciones enviadas por Mensajes Masivos y la respuesta de cada paciente.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('configuracion.mensajes-masivos') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium">
                    <span class="material-icons text-base mr-2">arrow_back</span> Volver
                </a>
                <button type="button" wire:click="exportar" wire:loading.attr="disabled" wire:target="exportar"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium shadow-md disabled:opacity-50">
                    <span wire:loading.remove wire:target="exportar" class="material-icons text-base mr-2">download</span>
                    <svg wire:loading wire:target="exportar" class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                    Exportar a Excel
                </button>
            </div>
        </div>

        {{-- Totales (clic para filtrar por respuesta) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <button type="button" wire:click="filtrarEstado('')" class="text-left bg-white rounded-xl border p-4 shadow-sm transition {{ $estado === '' ? 'border-[#2c4370] ring-2 ring-[#2c4370]/20' : 'border-gray-200 hover:border-gray-300' }}">
                <p class="text-xs text-slate-500 flex items-center"><span class="material-icons text-sm mr-1">mark_email_read</span> Citaciones enviadas</p>
                <p class="text-2xl font-bold text-[#2c4370]">{{ $totalEnviadas }}</p>
            </button>
            <button type="button" wire:click="filtrarEstado('asistira')" class="text-left bg-white rounded-xl border p-4 shadow-sm transition {{ $estado === 'asistira' ? 'border-green-600 ring-2 ring-green-600/20' : 'border-gray-200 hover:border-gray-300' }}">
                <p class="text-xs text-slate-500 flex items-center"><span class="material-icons text-sm mr-1 text-green-600">event_available</span> Confirmaron asistencia</p>
                <p class="text-2xl font-bold text-green-600">{{ $totalAsistira }}</p>
            </button>
            <button type="button" wire:click="filtrarEstado('no_asistira')" class="text-left bg-white rounded-xl border p-4 shadow-sm transition {{ $estado === 'no_asistira' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                <p class="text-xs text-slate-500 flex items-center"><span class="material-icons text-sm mr-1 text-red-500">event_busy</span> No asistirán</p>
                <p class="text-2xl font-bold text-red-500">{{ $totalNoAsistira }}</p>
            </button>
            <button type="button" wire:click="filtrarEstado('pendiente')" class="text-left bg-white rounded-xl border p-4 shadow-sm transition {{ $estado === 'pendiente' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                <p class="text-xs text-slate-500 flex items-center"><span class="material-icons text-sm mr-1 text-amber-500">hourglass_empty</span> Sin responder</p>
                <p class="text-2xl font-bold text-amber-500">{{ $totalPendiente }}</p>
            </button>
        </div>

        @if ($totalFallido)
            <button type="button" wire:click="filtrarEstado('fallido')" class="w-full text-left mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 flex items-center text-sm hover:bg-red-100">
                <span class="material-icons mr-2">mail_lock</span>
                {{ $totalFallido }} correo(s) no se pudieron entregar y no cuentan como enviados. Clic para verlos.
            </button>
        @endif

        {{-- Entrega del correo: "enviada" solo dice que la citación se encoló. --}}
        <div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <p class="text-xs font-semibold text-slate-600 uppercase tracking-wider mb-3">Salida de los correos</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                <div class="flex items-center">
                    <span class="material-icons text-base mr-2 text-green-600">outgoing_mail</span>
                    <span><strong class="text-green-700">{{ number_format($totalEntregadas) }}</strong> entregados al servidor de correo</span>
                </div>
                <div class="flex items-center">
                    <span class="material-icons text-base mr-2 {{ $totalEnCola ? 'text-amber-600' : 'text-gray-400' }}">schedule_send</span>
                    <span><strong class="{{ $totalEnCola ? 'text-amber-700' : 'text-gray-500' }}">{{ number_format($totalEnCola) }}</strong> todavía en la cola, sin salir</span>
                </div>
                <div class="flex items-center">
                    <span class="material-icons text-base mr-2 text-gray-400">help_outline</span>
                    <span><strong class="text-gray-500">{{ number_format($totalSinDato) }}</strong> sin dato (envíos anteriores)</span>
                </div>
            </div>
            @if ($totalEnCola)
                <p class="text-xs text-amber-800 mt-3 bg-amber-50 border border-amber-200 rounded p-2">
                    <strong>Atención:</strong> esos {{ number_format($totalEnCola) }} correo(s) aún no han salido. Salen solos con el proceso
                    que vacía la cola cada minuto; si no avanzan, revise que el cron de <code>schedule:run</code> esté activo en el servidor.
                </p>
            @endif
            @if ($totalSinDato)
                <p class="text-xs text-slate-500 mt-2">
                    El registro de la entrega empezó el {{ \Carbon\Carbon::parse(\App\Models\CitacionMasiva::SEGUIMIENTO_DESDE)->format('d/m/Y') }}.
                    De los envíos anteriores no se puede saber si el correo salió.
                </p>
            @endif
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                    <div class="relative">
                        <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base">search</span>
                        <input type="text" wire:model.debounce.400ms="busqueda" placeholder="Paciente, documento, correo..."
                               class="pl-9 w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Respuesta</label>
                    <select wire:model="estado" class="w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                        <option value="">Todas</option>
                        @foreach (\App\Models\CitacionMasiva::ESTADOS as $clave => $texto)
                            <option value="{{ $clave }}">{{ $texto }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Envío (archivo)</label>
                    <select wire:model="lote" class="w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                        <option value="">Todos los envíos</option>
                        @foreach ($lotes as $l)
                            <option value="{{ $l->lote }}">{{ \Carbon\Carbon::parse($l->enviado)->format('d/m/Y h:i A') }} - {{ $l->archivo ?: 'Sin nombre' }} ({{ $l->total }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Enviado desde</label>
                    <input type="date" wire:model="desde" class="w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Enviado hasta</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model="hasta" class="w-full rounded-lg border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                        <button type="button" wire:click="limpiarFiltros" title="Limpiar filtros" class="px-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50">
                            <span class="material-icons text-base">filter_alt_off</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listado --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="px-4 py-3">Fecha envío</th>
                            <th class="px-4 py-3">Nombre del paciente</th>
                            <th class="px-4 py-3">Número de documento</th>
                            <th class="px-4 py-3">Correo</th>
                            <th class="px-4 py-3">Especialidad</th>
                            <th class="px-4 py-3">Cita</th>
                            <th class="px-4 py-3">Especialista</th>
                            <th class="px-4 py-3">Sede</th>
                            <th class="px-4 py-3">Respuesta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($citaciones as $c)
                            <tr wire:key="citacion-{{ $c->id }}" class="hover:bg-blue-50/40">
                                <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ ($c->enviado_at ?? $c->created_at)->format('d/m/Y h:i A') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $c->paciente }}</td>
                                <td class="px-4 py-3 font-mono text-gray-800">{{ $c->documento }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $c->email }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $c->especialidad }}</td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                    {{ $c->dia }} {{ $c->fecha_cita }}
                                    <span class="block text-xs text-gray-500">{{ $c->hora }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $c->especialista }}</td>
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $sedes[$c->sede]['nombre'] ?? $c->sede }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($c->estado === 'asistira')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Confirmó asistencia</span>
                                    @elseif ($c->estado === 'no_asistira')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">No asistirá</span>
                                    @elseif ($c->estado === 'fallido')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">Correo no entregado</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Sin responder</span>
                                    @endif
                                    @if ($c->respondido_at)
                                        <span class="block text-xs text-gray-400 mt-0.5">{{ $c->respondido_at->format('d/m/Y h:i A') }}</span>
                                    @endif
                                    {{-- Entrega del correo, aparte de la respuesta del paciente. --}}
                                    @if ($c->entregado_at)
                                        <span class="block text-xs text-green-700 mt-0.5" title="El servidor de correo aceptó el mensaje">
                                            Correo enviado {{ $c->entregado_at->format('d/m/Y h:i A') }}
                                        </span>
                                    @elseif ($c->estado !== 'fallido' && $c->enviado_at && $c->enviado_at->format('Y-m-d') >= \App\Models\CitacionMasiva::SEGUIMIENTO_DESDE)
                                        <span class="block text-xs text-amber-700 mt-0.5" title="Encolado pero todavía sin salir">
                                            Correo aún en la cola
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-gray-500">No hay citaciones que coincidan con los filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $citaciones->links() }}
            </div>
        </div>
    </div>
</div>
