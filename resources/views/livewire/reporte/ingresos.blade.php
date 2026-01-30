<div class="font-body bg-gray-100 min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-full mx-auto px-6 py-4">
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a class="hover:text-[#2e3a75]" href="{{ route('dashboard') }}">Inicio</a>
                <span class="material-symbols-outlined text-base">chevron_right</span>
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
                    <span class="material-symbols-outlined text-sm">filter_alt</span>
                    <span>Filtrar</span>
                </button>

                <!-- Botón Exportar Excel -->
                <button wire:click="exportarExcel" 
                        class="text-white px-4 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all shadow-md hover:opacity-90" 
                        style="background-color: #059669;">
                    <span class="material-symbols-outlined text-sm">download</span>
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
                                        <span class="material-symbols-outlined text-xs mr-1">login</span>
                                        Ingreso
                                    </span>
                                @elseif($actividad->tipo_actividad === 'logout')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        <span class="material-symbols-outlined text-xs mr-1">logout</span>
                                        Salida
                                    </span>
                                @elseif($actividad->tipo_actividad === 'registro')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        <span class="material-symbols-outlined text-xs mr-1">person_add</span>
                                        Registro
                                    </span>
                                @elseif($actividad->tipo_actividad === 'cita')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                        <span class="material-symbols-outlined text-xs mr-1">event</span>
                                        Cita
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        <span class="material-symbols-outlined text-xs mr-1">touch_app</span>
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

    <!-- Material Symbols Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #2e3a75 !important;
            box-shadow: 0 0 0 3px rgba(46, 58, 117, 0.1) !important;
        }
    </style>
</div>
