<div class="font-body bg-gray-100 min-h-screen">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>
        .font-body { font-family: 'Inter', sans-serif; }
        .switch { position: relative; display: inline-block; width: 38px; height: 22px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .switch .pista { position: absolute; inset: 0; cursor: pointer; background: #cbd5e1; border-radius: 9999px; transition: background .15s; }
        .switch .pista:before { content: ""; position: absolute; width: 16px; height: 16px; left: 3px; top: 3px; background: #fff; border-radius: 9999px; transition: transform .15s; box-shadow: 0 1px 2px rgba(0,0,0,.25); }
        .switch input:checked + .pista { background: #2c4370; }
        .switch input:checked + .pista:before { transform: translateX(16px); }
        .switch input:disabled + .pista { cursor: not-allowed; opacity: .45; }
        .switch input:focus-visible + .pista { outline: 2px solid #93c5fd; outline-offset: 2px; }
    </style>

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center">
            <div class="flex items-center text-sm">
                <span class="text-slate-500">Configuración servicios</span>
                <span class="material-icons text-xs mx-2 text-slate-400">chevron_right</span>
                <span class="text-[#2c4370] font-medium">Gestor de permisos</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2c4370]/10 text-[#2c4370]">
                    <span class="material-icons">vpn_key</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#2c4370]">Gestor de permisos</h1>
                    <p class="text-sm text-slate-500">Controla el acceso de cada rol a las opciones del sistema y sus acciones de ver, crear, editar y borrar.</p>
                </div>
            </div>
            <button type="button" wire:click="guardar" wire:loading.attr="disabled" @if(!$rolActivo) disabled @endif
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm bg-[#2c4370] hover:bg-[#1e2f4d] disabled:opacity-50">
                <span class="material-icons text-base" wire:loading.remove wire:target="guardar">save</span>
                <span class="material-icons text-base animate-spin" wire:loading wire:target="guardar">autorenew</span>
                Guardar permisos
            </button>
        </div>

        {{-- Estadísticas --}}
        <div class="grid gap-4 sm:grid-cols-3">
            @foreach ([
                ['icon' => 'badge', 'valor' => $roles->count(), 'label' => 'Roles configurables', 'color' => 'bg-blue-100 text-blue-700'],
                ['icon' => 'view_quilt', 'valor' => $totalVistas, 'label' => 'Vistas administradas', 'color' => 'bg-violet-100 text-violet-700'],
                ['icon' => 'task_alt', 'valor' => $activas.' / '.$totalAcciones, 'label' => 'Acciones activas del rol', 'color' => 'bg-emerald-100 text-emerald-700'],
            ] as $card)
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $card['color'] }}">
                        <span class="material-icons">{{ $card['icon'] }}</span>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-slate-800">{{ $card['valor'] }}</div>
                        <div class="text-sm text-slate-500">{{ $card['label'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Selector de rol --}}
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Selecciona el rol a configurar</div>
            <div class="flex flex-wrap gap-2">
                @forelse ($roles as $rol)
                    <button type="button" wire:click="seleccionarRol({{ $rol->id }})" wire:key="rol-{{ $rol->id }}"
                        class="flex items-center gap-2 rounded-lg border px-3 py-2 text-sm font-medium transition-colors {{ $rolActivo && $rolActivo->id === $rol->id ? 'border-[#2c4370] bg-[#2c4370] text-white' : 'border-gray-200 bg-white text-slate-700 hover:bg-gray-50' }}">
                        <span class="material-icons text-base">shield</span>
                        {{ $rol->name }}
                    </button>
                @empty
                    <p class="text-sm text-slate-500">No hay roles para configurar.</p>
                @endforelse
            </div>
            <p class="mt-2 text-xs text-slate-500">
                El rol Super Admin siempre tiene acceso total y no se configura aquí. El Gestor de permisos es exclusivo del Super Admin.
            </p>
        </div>

        {{-- Roles que el rol puede asignar al crear o editar usuarios --}}
        @if ($rolActivo)
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <span class="material-icons text-base">manage_accounts</span>
                Roles que {{ $rolActivo->name }} puede asignar a los usuarios
            </div>
            <p class="mb-3 text-xs text-slate-500">
                Define hasta qué nivel puede llegar este rol al crear o editar usuarios en «Consultar usuarios»: solo verá y podrá asignar los roles marcados.
                Requiere además el permiso Editar de «Consultar usuarios». Super Admin solo lo asigna un Super Admin.
            </p>
            <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($candidatosAsignables as $candidato)
                    @php $marcado = in_array((string) $candidato->id, array_map('strval', $asignables), true); @endphp
                    <label wire:key="asignable-{{ $candidato->id }}"
                        class="flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm {{ $marcado ? 'border-[#2c4370] bg-[#2c4370]/5 text-[#2c4370]' : 'border-gray-200 text-slate-700 hover:bg-gray-50' }}">
                        <input type="checkbox" value="{{ $candidato->id }}" wire:model="asignables" @if($marcado) checked @endif
                            class="rounded border-gray-300 text-[#2c4370] focus:ring-[#2c4370]">
                        <span class="material-icons text-base">shield</span>
                        {{ $candidato->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Matriz de permisos --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-2 border-b border-gray-200 p-3 sm:flex-row sm:items-center sm:justify-between">
                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    Matriz de permisos{{ $rolActivo ? ' — '.$rolActivo->name : '' }}
                </span>
                <div class="flex gap-2">
                    <button type="button" wire:click="marcarTodo" title="Activar todas las vistas y acciones"
                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm text-slate-700 hover:bg-gray-50">
                        <span class="material-icons text-base">done_all</span> Marcar todo
                    </button>
                    <button type="button" wire:click="limpiarTodo" title="Quitar todas las vistas y acciones del rol"
                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm text-amber-700 hover:bg-amber-50">
                        <span class="material-icons text-base">backspace</span> Limpiar todo
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Opción / Vista</th>
                            @foreach ($acciones as $accion => $label)
                                <th class="px-4 py-3 text-center font-medium">
                                    <button type="button" wire:click="alternarColumna('{{ $accion }}')" title="Alternar {{ $label }} en todas las vistas"
                                        class="inline-flex items-center gap-1 rounded-md px-2 py-1 uppercase hover:bg-gray-200 hover:text-slate-800">
                                        {{ $label }}
                                    </button>
                                </th>
                            @endforeach
                            <th class="px-4 py-3 font-medium">Otras acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($grupos as $grupo)
                            <tr class="bg-gray-50/70" wire:key="grupo-{{ $loop->index }}">
                                <td colspan="{{ count($acciones) + 2 }}" class="px-4 py-2 text-xs font-bold uppercase tracking-wide text-[#2c4370]">{{ $grupo }}</td>
                            </tr>
                            @foreach ($vistas as $vista)
                                @continue($vista['grupo'] !== $grupo)
                                @php
                                    $claveVer = isset($vista['acciones']['ver']) ? $this->clave($vista['acciones']['ver']) : null;
                                    $verApagado = $claveVer && empty($marcados[$claveVer]);
                                @endphp
                                <tr wire:key="vista-{{ $vista['key'] }}" class="transition-colors hover:bg-gray-50 {{ $verApagado ? 'opacity-60' : '' }}">
                                    <td class="px-4 py-3">
                                        <button type="button" wire:click="alternarVista('{{ $vista['key'] }}')" title="Alternar todas las acciones de esta vista"
                                            class="text-left font-medium text-slate-800 hover:text-[#2c4370]">{{ $vista['titulo'] }}</button>
                                        <div class="font-mono text-xs text-slate-400">{{ $vista['ruta'] }}</div>
                                    </td>
                                    @foreach ($acciones as $accion => $label)
                                        <td class="px-4 py-3 text-center">
                                            @if (isset($vista['acciones'][$accion]))
                                                @php $permiso = $vista['acciones'][$accion]; @endphp
                                                <label class="switch" title="{{ $permiso }}">
                                                    <input type="checkbox" wire:model="marcados.{{ $this->clave($permiso) }}" @if(!empty($marcados[$this->clave($permiso)])) checked @endif
                                                        aria-label="{{ $label }} en {{ $vista['titulo'] }}"
                                                        @if ($accion !== 'ver' && $verApagado) disabled @endif>
                                                    <span class="pista"></span>
                                                </label>
                                            @else
                                                <span class="text-slate-300">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col gap-1.5">
                                            @forelse ($vista['otras'] ?? [] as $permiso => $label)
                                                <label class="inline-flex items-center gap-2 text-xs text-slate-600" title="{{ $permiso }}">
                                                    <span class="switch">
                                                        <input type="checkbox" wire:model="marcados.{{ $this->clave($permiso) }}" @if(!empty($marcados[$this->clave($permiso)])) checked @endif
                                                            @if ($verApagado) disabled @endif>
                                                        <span class="pista"></span>
                                                    </span>
                                                    {{ $label }}
                                                </label>
                                            @empty
                                                <span class="text-slate-300">—</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-center justify-between gap-3 border-t border-gray-200 p-4 sm:flex-row">
                <div class="space-y-1 text-xs text-slate-500">
                    <p>Si «Ver» está apagado, el rol no ve la opción en el menú ni puede abrir la vista, y sus demás acciones quedan bloqueadas.</p>
                    <p>Pase el cursor sobre un interruptor para ver el permiso que controla. Los cambios aplican al guardar y los usuarios del rol los ven al recargar la página.</p>
                </div>
                <button type="button" wire:click="guardar" wire:loading.attr="disabled" @if(!$rolActivo) disabled @endif
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm bg-[#2c4370] hover:bg-[#1e2f4d] disabled:opacity-50">
                    <span class="material-icons text-base">save</span>
                    Guardar permisos
                </button>
            </div>
        </div>
    </div>
</div>
