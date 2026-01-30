<div>
    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
        <div class="flex justify-center min-h-max pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            
            <div class="fixed inset-0 trasition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-x1 transform trasition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="mb-4 text-center">
                        <label class="text-2xl" for="">Edición de eps</label>
                    </div>
                    <form wire:submit.prevent="editar" id="EditarForm">
                    <div class="rounded-lg grid grid-cols-6 gap-6 mb-4">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="eps.nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                                <input  type="text" id="eps.nombre" wire:model="eps_nombre" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                @error('eps_nombre') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="eps.estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <select wire:model.defer="eps_estado" id="eps.estado" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @if($eps_estado == true)
                                    <option value="true" selected>Activo</option>
                                    <option value="false">Inactivo</option>
                                    @else
                                    <option value="true">Activo</option>
                                    <option value="false" selected>Inactivo</option>
                                    @endif
                                </select>
                                @error('eps_estado') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                            </div>
                        </form>
                     
                        <div class="grid grid-cols-6 gap-6 space-x-20">
                            <div class="col-span-6 sm:col-span-3">
                                <button wire:target="editar" class="transition ease-in-out delay-150 bg-green-600 hover:-translate-y-1 hover:scale-110 hover:bg-green-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white"
                                form="EditarForm">
                                    Actualizar
                                </button>
                            </div>
                            <div class="block col-span-6 sm:col-span-3">
                                <button wire:click.prevent="cerrarModal()" type="button" class="transition ease-in-out delay-150 bg-red-600 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
