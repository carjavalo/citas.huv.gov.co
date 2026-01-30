<div>
    @if($modal)
        <div>
            @livewire('servicios.editar', ['serv_id' => $serv_id]) 
        </div>
    @endif
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-200 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- This example requires Tailwind CSS v2.0+ -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Servicio
                                                <input type="text" wire:model="busqueda" id="" placeholder="Filtrar por nombre" class="mt-1 inline focus:ring-blue-500 focus:blue-indigo-500 w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Estado
                                                <!-- <select wire:model="estado" type="text" id="ubicacion" autocomplete="off" class="mt-1 inline-block min-w-fit focus:ring-blue-500 focus:blue-indigo-500 shadow-sm sm:text-sm border-gray-300 rounded-md" >
                                                    <option value="" selected>Seleccione...</option>
                                                    <option value="true">Activo</option>
                                                    <option value="false">Inactivo</option>
                                                </select> -->
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($servicios as $servicio)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">                                      
                                                    {{ $servicio->servnomb }}                                                  
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    @if($servicio->estado == true)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"> Activos </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"> Inactivo </span>
                                                    @endif
                                                </td>
                                                
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <button wire:click="abrirModal({{ $servicio->id }})" class="justify-center transition ease-in-out delay-150 bg-yellow-500 hover:-translate-y-1 hover:scale-110 hover:bg-yellow-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md">Editar</button>
                                                    <button onclick="confirmarEliminacion({{ $servicio->id }})" class="justify-center transition ease-in-out delay-150 bg-red-500 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white ml-2">Eliminar</button>
                                                </td>
                                                
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $servicios->links() }}
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
                    Livewire.emit('eliminarServicio', id);
                }
            })
        }
    </script>
</div>
