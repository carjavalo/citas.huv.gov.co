<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Encabezado -->
            <div class="mb-6 p-4 rounded-lg shadow-md" style="background-color: #2c4370;">
                <h2 class="text-2xl font-bold text-white text-center">
                    <svg class="inline-block w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Gestión de Roles de Usuarios
                </h2>
                <p class="text-center text-gray-200 mt-1">Vista exclusiva para Super Administrador</p>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- Filtro de búsqueda por rol -->
                <div class="p-4 border-b" style="background-color: #e8ecf3;">
                    <div class="max-w-xs">
                        <label class="block text-sm font-medium mb-1" style="color: #2c4370;">Filtrar por Rol</label>
                        <select wire:model="filtroRol"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:border-transparent">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tabla de usuarios -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #2c4370;">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    #
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Nombre Completo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                    Identificación
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">
                                    Rol Asignado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($usuarios as $index => $usuario)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ($usuarios->currentPage() - 1) * $usuarios->perPage() + $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: #2c4370;">
                                                {{ strtoupper(substr($usuario->name, 0, 1)) }}{{ strtoupper(substr($usuario->apellido1, 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $usuario->name }} {{ $usuario->apellido1 }} {{ $usuario->apellido2 }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-mono">{{ $usuario->ndocumento }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if ($usuario->rol_nombre)
                                            @php
                                                $bgColor = match($usuario->rol_nombre) {
                                                    'Super Admin' => 'background-color: #dc2626;',
                                                    'Administrador' => 'background-color: #2563eb;',
                                                    'Coordinador' => 'background-color: #7c3aed;',
                                                    'Consultor' => 'background-color: #059669;',
                                                    'Usuario' => 'background-color: #6b7280;',
                                                    default => 'background-color: #2c4370;'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white" style="{{ $bgColor }}">
                                                {{ $usuario->rol_nombre }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-600">
                                                Sin rol asignado
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No se encontraron usuarios</p>
                                            <p class="text-sm">Intente modificar los filtros de búsqueda</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200" style="background-color: #f8fafc;">
                    {{ $usuarios->links() }}
                </div>

                <!-- Resumen de totales por rol -->
                <div class="p-4 border-t" style="background-color: #e8ecf3;">
                    <h3 class="text-sm font-semibold mb-3" style="color: #2c4370;">Resumen de Usuarios por Rol</h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach($roles as $rol)
                            @php
                                $count = \App\Models\User::whereHas('roles', function($q) use ($rol) {
                                    $q->where('name', $rol->name);
                                })->count();
                                $bgColor = match($rol->name) {
                                    'Super Admin' => 'background-color: #dc2626;',
                                    'Administrador' => 'background-color: #2563eb;',
                                    'Coordinador' => 'background-color: #7c3aed;',
                                    'Consultor' => 'background-color: #059669;',
                                    'Usuario' => 'background-color: #6b7280;',
                                    default => 'background-color: #2c4370;'
                                };
                            @endphp
                            <div class="inline-flex items-center px-3 py-2 rounded-lg text-white text-sm" style="{{ $bgColor }}">
                                <span class="font-semibold mr-2">{{ $rol->name }}:</span>
                                <span class="bg-white text-black px-2 py-0.5 rounded font-semibold">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                    @if(!$esSuperAdmin)
                    <p class="text-xs text-gray-500 mt-3 italic">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        La información de Super Admin está restringida.
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
