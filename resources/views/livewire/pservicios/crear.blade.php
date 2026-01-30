<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $modoEdicion ? 'Editar Servicio' : 'Crear Nuevo Servicio' }}
                        </h3>
                        <div class="mt-4">
                            <div class="mb-4">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción del Servicio</label>
                                <input type="text" wire:model="descripcion" id="descripcion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Ingrese la descripción del servicio">
                                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-4">
                                <label for="sede_id" class="block text-sm font-medium text-gray-700">Sede</label>
                                <select wire:model="sede_id" id="sede_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Seleccione una sede...</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}">{{ $sede->nombre }} - {{ $sede->ciudad }}</option>
                                    @endforeach
                                </select>
                                @error('sede_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            @if($modoEdicion)
                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="activo" id="activo" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-3 text-sm font-medium text-gray-700">Servicio Habilitado</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Desmarque esta opción para deshabilitar el servicio y que no esté disponible para los usuarios.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button wire:click="guardar" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ $modoEdicion ? 'Actualizar' : 'Guardar' }}
                </button>
                <button wire:click="cerrar" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
