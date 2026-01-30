<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
    <div class="flex justify-center min-h-max pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 trasition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-x1 transform trasition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="bg-blue-100 rounded-lg mb-4 text-center">
                    <label class="text-2xl" for="">Información de la cita</label>
                </div>
                <div class="bg-gray-100 rounded-lg grid grid-cols-6 gap-6 mb-2 select-none">
                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label for="">Paciente:</label>
                        <p class="lg:text-lg"> {{ $datos->name }} {{ $datos->apellido1 }} {{ $datos->apellido2 }} </p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label>Numero de documento:</label>
                        <p class="lg:text-lg"> {{ $datos->ndocumento }} </p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label>Especialidad:</label>
                        <p class="lg:text-lg"> {{ $datos->servnomb }} </p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label>Fecha de solicitud:</label>
                        <p class="lg:text-lg"> {{ $datos->created_at }} </p>
                    </div>

                </div>

                <div class="grid grid-cols-6 gap-6">

                    <div class="col-span-6 sm:col-span-6">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo a notificar</label>
                        <input type="text" id="email" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm lg:text-lg border-gray-300 rounded-md" value="{{ $datos->email }}" disabled>
                    </div>

                    <div class="col-span-6 sm:col-span-6">
                        <div class="mb-3 w-96">
                            <label for="obs" class="block w-full text-sm font-medium text-gray-700">Observación</label>
                            <textarea wire:model.defer="mensaje" id="obs" class="rounded-lg border border-solid border-gray-300" cols="46" rows="5"></textarea>
                            @error('mensaje') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <button wire:click.prevent="notificar()" onclick="confirm('¿Está segur@ de rechazar esta cita?') || event.stopImmediatePropagation()" class="w-full justify-center transition ease-in-out delay-150 bg-green-600 hover:-translate-y-1 hover:scale-110 hover:bg-green-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                            Enviar
                        </button>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <button wire:click.prevent="$emitTo('citas.consulta-general','cerrarRechazar')" type="button" class="w-full justify-center transition ease-in-out delay-150 bg-red-600 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">Cancelar</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>