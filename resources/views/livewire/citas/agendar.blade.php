<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-100 overflow-hidden shadow-xl sm:rounded-lg sm:border-2">
                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0">
                                <h3 class="mt-2 text-lg font-medium leading-6 text-gray-900">Importante</h3>
                                <p class="mt-1 ml-4 text-sm text-gray-600">
                                    Estimado usuario, los campos marcados con un ( <span class="text-red-500">*</span> ) son obligatorios, por lo tanto debes diligenciar la información requerida.
                                </p>
                            </div>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form>
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                                                <input wire:model="nombres" type="text" name="nombres" id="nombres" autocomplete="given-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->name }}" disabled>
                                            </div>
                                            <input wire:model="pacid" type="hidden" id="pacid" name="pacid" value="{{ Auth::user()->id }}">
                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="apellido1" class="block text-sm font-medium text-gray-700">Primer apellido</label>
                                                <input wire:model="apellido1" type="text" name="apellido1" id="apellido1" autocomplete="family-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->apellido1 }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="apellido2" class="block text-sm font-medium text-gray-700">Segundo apellido</label>
                                                <input wire:model="apellido2" type="text" name="apellido2" id="apellido2" autocomplete="family-name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->apellido2 }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="correo" class="block text-sm font-medium text-gray-700">Correo electrónico <span class="text-red-500">*</span></label>
                                                <input wire:model="correo" type="text" name="correo" id="correo" autocomplete="email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="" class="block text-sm font-medium text-gray-700">Mi EPS</label>
                                                <input wire:model="eps" type="text" id="" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="espec" class="block text-sm font-medium text-gray-700">Servicio<span class="text-red-500">*</span></label>
                                                <select wire:model="espec" id="espec" name="espec" autocomplete="country-name" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="#">Seleccione...</option>
                                                    <option value="1">Pediatria</option>
                                                    <option value="2">Fisiatria</option>
                                                    <option value="3">Bacteriologo</option>
                                                    <option value="4">Neuropsicologia</option>
                                                </select>
                                                @error('espec') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                            </div>


                                            <div class="col-span-6 sm:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="historia" class="block text-sm font-medium text-gray-700">Historia Clinica<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model="historia" id="historia">
                                                    @error('historia') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="autorizacion" class="block text-sm font-medium text-gray-700">Autorizacion EPS<span class="text-red-500">*</span></label>
                                                    <input class="" type="file" wire:model="autorizacion" id="autorizacion">
                                                    @error('autorizacion') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="ordenMedica" class="block text-sm font-medium text-gray-700">Orden médica<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model="ordenMedica" id="ordenMedica">
                                                    @error('ordenMedica') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="documento_identidad" class="block text-sm font-medium text-gray-700">Documento de identidad<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model="pacdocid" id="documento_identidad">
                                                    @error('pacdocid') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                                <button wire:click.prevent="registrar()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">

                                                    Registrar

                                                </button>
                                            </span>

                                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                                <button wire:click="$emitTo('paciente.index','cerrarModal')" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">

                                                    Cancelar

                                                </button>
                                            </span>
                                        </div>
                                        <div class="col-span-6 sm:col-span-6">
                                            <div class="mb-3 w-96">
                                                <label for="obs" class="block text-sm font-medium text-gray-700">Observación<span class="text-red-500">*</span></label>
                                                <textarea wire:model="observacion" id="obs" class="" cols="79" rows="10"></textarea>
                                            </div>
                                        </div>

                                        <div class="px-4 py-3 text-center sm:px-6">
                                            <button wire:click.prevent="agendar()" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Enviar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>