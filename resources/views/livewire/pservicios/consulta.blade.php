<div>
    @if($modalCrear)
        <div>
            @livewire('pservicios.crear') 
        </div>
    @endif
    @if($modal)
        <div>
            @livewire('pservicios.crear', ['pservicio_id' => $pservicio_id]) 
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-200 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="p-4 flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-800">Gestión de Servicios</h2>
                                <button wire:click="abrirModalCrear" class="transition ease-in-out delay-150 bg-green-500 hover:-translate-y-1 hover:scale-110 hover:bg-green-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Servicio
                                </button>
                            </div>
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Descripción
                                                <input type="text" wire:model.debounce.300ms="busqueda" placeholder="Buscar servicio..." class="mt-1 block w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sede
                                                <select wire:model="filtroSede" class="mt-1 block w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    <option value="">Todas las sedes</option>
                                                    @foreach($sedes as $sede)
                                                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($pservicios as $pservicio)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">                                      
                                                    {{ $pservicio->descripcion }}                                                  
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if($pservicio->sede)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ $pservicio->sede->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            Sin sede asignada
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <button wire:click="abrirModalEditar({{ $pservicio->id }})" class="transition ease-in-out delay-150 bg-yellow-500 hover:-translate-y-1 hover:scale-110 hover:bg-yellow-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                                        Editar
                                                    </button>
                                                    <button onclick="confirmarEliminacion({{ $pservicio->id }})" class="transition ease-in-out delay-150 bg-red-500 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white ml-2">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                                    No se encontraron servicios
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="px-4 py-3">
                                    {{ $pservicios->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('eliminarPservicio', id);
                }
            })
        }
    </script>
</div>
