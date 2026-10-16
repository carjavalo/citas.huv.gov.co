<div class="font-body bg-gray-100 min-h-screen">
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <style>.font-body { font-family: 'Inter', sans-serif; }</style>

    {{-- Breadcrumb --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-12 flex items-center">
            <div class="flex items-center text-sm">
                <span class="text-slate-500">Configuración servicios</span>
                <span class="material-icons text-xs mx-2 text-slate-400">chevron_right</span>
                <span class="text-[#2c4370] font-medium">Gestión Roles</span>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        {{-- Encabezado --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#2c4370]/10 text-[#2c4370]">
                    <span class="material-icons">admin_panel_settings</span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#2c4370]">Gestión Roles</h1>
                    <p class="text-sm text-slate-500">Cree, edite y elimine los roles del sistema. Sus permisos se configuran en el Gestor de permisos.</p>
                </div>
            </div>
            @can('admin.roles.crear')
            <button type="button" wire:click="crear"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white shadow-sm bg-[#2c4370] hover:bg-[#1e2f4d]">
                <span class="material-icons text-base">add</span> Nuevo rol
            </button>
            @endcan
        </div>

        {{-- Listado --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 p-3">
                <div class="relative max-w-sm">
                    <span class="material-icons absolute left-2 top-1/2 -translate-y-1/2 text-base text-slate-400">search</span>
                    <input type="text" wire:model.debounce.300ms="busqueda" placeholder="Buscar rol..."
                        class="w-full rounded-md border-gray-300 pl-8 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Rol</th>
                            <th class="px-4 py-3 font-medium">Descripción</th>
                            <th class="px-4 py-3 text-center font-medium">Sede y servicio</th>
                            <th class="px-4 py-3 text-center font-medium">Usuarios</th>
                            <th class="px-4 py-3 text-center font-medium">Permisos</th>
                            <th class="px-4 py-3 text-right font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($roles as $rol)
                            @php
                                $esSuper = $rol->name === 'Super Admin';
                                $esSistema = in_array($rol->name, $rolesSistema, true);
                            @endphp
                            <tr wire:key="rol-{{ $rol->id }}" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 font-medium text-slate-800">
                                        <span class="material-icons text-base text-[#2c4370]">shield</span>
                                        {{ $rol->name }}
                                        @if ($esSistema)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-500" title="El código usa este nombre: no se puede renombrar ni eliminar">Sistema</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-slate-600">{{ $rol->descripcion ?: '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($rol->requiere_sede)
                                        <span class="material-icons text-base text-emerald-600" title="Al editar un usuario con este rol se pide sede y servicio">check_circle</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex min-w-[2rem] justify-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $rol->usuarios_count }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-slate-600">{{ $esSuper ? 'Todos' : $rol->permisos_count }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-1">
                                        @unless ($esSuper)
                                            @role('Super Admin')
                                            <a href="{{ route('configuracion.permisos', ['role' => $rol->id]) }}" title="Configurar permisos"
                                                class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-gray-100 hover:text-[#2c4370]">
                                                <span class="material-icons text-lg">vpn_key</span>
                                            </a>
                                            @endrole
                                            @can('admin.roles.editar')
                                            <button type="button" wire:click="editar({{ $rol->id }})" title="Editar"
                                                class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-gray-100 hover:text-amber-600">
                                                <span class="material-icons text-lg">edit</span>
                                            </button>
                                            @endcan
                                            @can('admin.roles.eliminar')
                                                @unless ($esSistema)
                                                <button type="button" title="Eliminar"
                                                    onclick="if(confirm('¿Eliminar el rol {{ addslashes($rol->name) }}? Esta acción no se puede deshacer.')) { @this.call('eliminar', {{ $rol->id }}) }"
                                                    class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-red-50 hover:text-red-600">
                                                    <span class="material-icons text-lg">delete</span>
                                                </button>
                                                @endunless
                                            @endcan
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">No se encontraron roles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 p-3">{{ $roles->links() }}</div>
        </div>

        <p class="text-xs text-slate-500">
            Después de crear un rol, configure en el <strong>Gestor de permisos</strong> qué vistas y acciones tiene y qué roles puede asignar.
            Los usuarios nuevos o registrados reciben el rol <strong>Usuario</strong>; se les cambia el rol desde «Consultar usuarios».
        </p>
    </div>

    {{-- Modal crear / editar --}}
    @if ($modal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" wire:keydown.escape="cerrar">
        <div class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
            <div class="flex items-center justify-between px-5 py-3" style="background-color: #2c4370;">
                <h3 class="font-semibold text-white">{{ $rolId ? 'Editar rol' : 'Nuevo rol' }}</h3>
                <button type="button" wire:click="cerrar" class="text-white/80 hover:text-white"><span class="material-icons">close</span></button>
            </div>
            <form wire:submit.prevent="guardar" class="max-h-[75vh] space-y-4 overflow-y-auto p-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Nombre del rol <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="nombre" maxlength="100"
                        @if ($rolId && in_array($nombre, $rolesSistema, true)) readonly title="Rol del sistema: el nombre no se puede cambiar" @endif
                        class="w-full rounded-md border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370] read-only:bg-gray-100">
                    @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Descripción</label>
                    <textarea wire:model.defer="descripcion" rows="2" maxlength="255"
                        class="w-full rounded-md border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]"></textarea>
                    @error('descripcion') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <label class="flex items-start gap-2 text-sm text-slate-700">
                    <input type="checkbox" wire:model.defer="requiere_sede" class="mt-0.5 rounded border-gray-300 text-[#2c4370] focus:ring-[#2c4370]">
                    <span>
                        Requiere sede y servicio
                        <span class="block text-xs text-slate-500">Al asignar este rol a un usuario se le pide sede y servicio (como Consultor o Coordinador). Úselo para roles de personal que atienden solicitudes.</span>
                    </span>
                </label>

                @unless ($rolId)
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Copiar permisos de (opcional)</label>
                    <select wire:model.defer="copiar_de" class="w-full rounded-md border-gray-300 text-sm focus:border-[#2c4370] focus:ring-[#2c4370]">
                        <option value="">— Empezar sin permisos —</option>
                        @foreach ($otrosRoles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Copia sus vistas, acciones y roles asignables; luego puede ajustarlos en el Gestor de permisos.</p>
                    @error('copiar_de') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                @endunless

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Roles que pueden asignar este rol a usuarios</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($asignadores as $r)
                            <label class="flex items-center gap-2 rounded-md border border-gray-200 px-2 py-1.5 text-sm text-slate-700 hover:bg-gray-50">
                                <input type="checkbox" value="{{ $r->id }}" wire:model.defer="asignadoPor" class="rounded border-gray-300 text-[#2c4370] focus:ring-[#2c4370]">
                                {{ $r->name }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Super Admin siempre puede asignarlo. También se configura en el Gestor de permisos.</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" wire:click="cerrar" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 rounded-lg bg-[#2c4370] px-4 py-2 text-sm font-medium text-white hover:bg-[#1e2f4d] disabled:opacity-50">
                        <span class="material-icons text-base">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
