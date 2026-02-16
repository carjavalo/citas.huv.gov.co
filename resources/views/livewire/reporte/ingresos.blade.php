<div class="bg-gray-100 min-h-screen" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-full mx-auto px-6 py-4">
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a class="hover:text-[#2e3a75]" href="{{ route('dashboard') }}">Inicio</a>
                <span>&rsaquo;</span>
                <span class="font-medium" style="color: #2e3a75;">Reporte de Actividades</span>
            </nav>
        </div>
    </div>

    <!-- Header -->
    <div class="px-6 py-6 border-b" style="background: linear-gradient(135deg, #2e3a75 0%, #3d4d8f 100%);">
        <div class="max-w-full mx-auto">
            <h2 class="text-2xl font-black tracking-tight text-white">
                Actividad de Usuarios
            </h2>
            <p class="text-white/80 mt-2 text-sm">
                Registro completo de ingresos, salidas y acciones realizadas en el sistema.
            </p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="max-w-full mx-auto">
            <div class="flex flex-wrap items-end gap-3">
                <!-- Fecha Inicio -->
                <div class="flex flex-col gap-1">
                    <label class="text-[9px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                        Inicio
                    </label>
                    <input wire:model="fechaInicio" 
                           class="w-36 bg-white border-gray-300 rounded-lg text-xs px-2.5 py-1.5 transition-colors" 
                           type="date"/>
                </div>

                <!-- Fecha Fin -->
                <div class="flex flex-col gap-1">
                    <label class="text-[9px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                        Fin
                    </label>
                    <input wire:model="fechaFin" 
                           class="w-36 bg-white border-gray-300 rounded-lg text-xs px-2.5 py-1.5 transition-colors" 
                           type="date"/>
                </div>

                <!-- Tipo -->
                <div class="flex flex-col gap-1">
                    <label class="text-[9px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                        Tipo
                    </label>
                    <select wire:model="tipoActividad" 
                            class="w-32 bg-white border-gray-300 rounded-lg text-xs px-2.5 py-1.5 transition-colors">
                        <option value="">Todos</option>
                        <option value="login">Ingreso</option>
                        <option value="logout">Salida</option>
                        <option value="registro">Registro</option>
                        <option value="cita">Cita</option>
                        <option value="accion">Acción</option>
                    </select>
                </div>

                <!-- Botón Filtrar -->
                <button wire:click="aplicarFiltros" 
                        class="text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all shadow-md hover:opacity-90" 
                        style="background-color: #2e3a75;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    <span>Filtrar</span>
                </button>

                <!-- Botón Exportar Excel -->
                <button wire:click="exportarExcel" 
                        class="text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all shadow-md hover:opacity-90" 
                        style="background-color: #059669;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Exportar Excel</span>
                </button>

                <!-- Estadísticas -->
                <div class="ml-auto flex items-center gap-2">
                    <div class="px-3 py-1.5 rounded-lg flex items-center gap-2" style="background-color: rgba(46, 58, 117, 0.1); border: 1px solid rgba(46, 58, 117, 0.3);">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold uppercase leading-none" style="color: rgba(46, 58, 117, 0.7);">
                                Total
                            </span>
                            <span class="text-lg font-bold leading-tight" style="color: #2e3a75;">{{ number_format($totalRegistros) }}</span>
                        </div>
                    </div>
                    <div class="bg-green-50 px-3 py-1.5 rounded-lg border border-green-200 flex items-center gap-2">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-green-600 uppercase leading-none">
                                Ingresos
                            </span>
                            <span class="text-green-700 text-lg font-bold leading-tight">{{ number_format($ingresos) }}</span>
                        </div>
                    </div>
                    <div class="bg-red-50 px-3 py-1.5 rounded-lg border border-red-200 flex items-center gap-2">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-red-600 uppercase leading-none">
                                Salidas
                            </span>
                            <span class="text-red-700 text-lg font-bold leading-tight">{{ number_format($salidas) }}</span>
                        </div>
                    </div>
                    <div class="bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200 flex items-center gap-2">
                        <div class="flex flex-col">
                            <span class="text-[8px] font-bold text-blue-600 uppercase leading-none">
                                Nuevos
                            </span>
                            <span class="text-blue-700 text-lg font-bold leading-tight">{{ number_format($nuevosUsuarios) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="px-6 py-6">
        <div class="max-w-full mx-auto bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b-2" style="background-color: rgba(46, 58, 117, 0.05); border-color: #2e3a75;">
                        <tr>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Documento
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Usuario
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Fecha
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Hora
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Tipo
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Descripción
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide" style="color: #2e3a75;">
                                Módulo
                            </th>
                            <th class="px-4 py-3 text-[10px] font-bold uppercase tracking-wide text-center" style="color: #2e3a75;">
                                IP
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($actividades as $actividad)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-xs font-mono font-medium" style="color: #2e3a75;">
                                {{ $actividad->user ? $actividad->user->ndocumento : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-7 rounded-full flex items-center justify-center text-white font-bold text-[10px]" style="background-color: #{{ substr(md5($actividad->user_id), 0, 6) }};">
                                        {{ $actividad->user ? strtoupper(substr($actividad->user->name, 0, 2)) : 'NA' }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">
                                        {{ $actividad->user ? $actividad->user->name . ' ' . $actividad->user->apellido1 : 'Usuario Eliminado' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                {{ \Carbon\Carbon::parse($actividad->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-xs font-medium">
                                {{ \Carbon\Carbon::parse($actividad->created_at)->format('H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if($actividad->tipo_actividad === 'login')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                        Ingreso
                                    </span>
                                @elseif($actividad->tipo_actividad === 'logout')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Salida
                                    </span>
                                @elseif($actividad->tipo_actividad === 'registro')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                        Registro
                                    </span>
                                @elseif($actividad->tipo_actividad === 'cita')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        Cita
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                                        Acción
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-700">{{ $actividad->descripcion }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs font-medium text-gray-600 capitalize">{{ $actividad->modulo ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-mono text-gray-500">{{ $actividad->ip_address ?? '-' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                No hay registros para el período seleccionado
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $actividades->links() }}
            </div>
        </div>
    </div>

    <style>
        input:focus, select:focus {
            outline: none;
            border-color: #2e3a75 !important;
            box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1) !important;
        }
    </style>
</div>
