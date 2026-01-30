<div >
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-blue-50 overflow-hidden shadow-xl sm:rounded-lg sm:border-2">
                <!-- padre -->
                   
                <div class="mt-10 sm:mt-0"  >
                    
                    
                    <div  class="md:grid md:grid-cols-3 md:gap-6"   >
                        <div class="md:col-span-1">
                            <div class="px-4 sm:px-0 mx-2 my-2">
                                <h3 class="mt-2 font-semibold leading-6 sm:text-sm md:text-xl xl:text-xl 2xl:text-xl"><b>Importante</b></h3>
                                <p class="mt-1 ml-4 sm:text-sm md:text-lg xl:text-xl text-justify">
                                    Estimado usuario, los campos marcados con un ( <span class="text-red-500">*</span> ) son obligatorios, por lo tanto debe diligenciar la información requerida.
                                </p>
                                <p class="mt-1 ml-4 sm:text-sm md:text-lg xl:text-xl text-justify">
                                    El límite máximo por cada archivo es de 2 Megabytes (2MB).
                                </p>
                                <p class="mt-1 ml-4 sm:text-sm md:text-lg xl:text-xl text-justify">
                                    Por favor, adjuntar documento de identidad escaneado por ambos lados.
                                </p>
                                <p class="mt-1 ml-4 sm:text-sm md:text-lg text-red-600 xl:text-xl text-justify">
                                    <b>Por favor adjuntar documentos legibles.</b>
                                </p>
                            </div>
                        </div>
                        <div class="mt-5 md:mt-0 md:col-span-2">
                            <form >
                                <div  class="shadow overflow-hidden sm:rounded-md">        
                                    <div  class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                <label for="nombres" class="block text-sm font-semibold">Nombres</label>
                                                <input wire:model="nombres" type="text" name="nombres" id="nombres" autocomplete="given-name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->name }}" disabled>
                                            </div>
                                            <input wire:model="pacid" type="hidden" id="pacid" name="pacid" value="{{ Auth::user()->id }}">
                                            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                <label for="apellido1" class="block text-sm font-semibold">Primer apellido</label>
                                                <input wire:model="apellido1" type="text" name="apellido1" id="apellido1" autocomplete="family-name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->apellido1 }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3 lg:col-span-2">
                                                <label for="apellido2" class="block text-sm font-semibold">Segundo apellido</label>
                                                <input wire:model="apellido2" type="text" name="apellido2" id="apellido2" autocomplete="family-name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->apellido2 }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="correo" class="block text-sm font-semibold">Correo electrónico</label>
                                                <input wire:model="correo" type="text" name="correo" id="correo" autocomplete="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}" disabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="" class="block text-sm font-semibold">Mi eps</label>
                                                <input wire:model="eps" type="text" id="" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm" enabled>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3">
                                                <label for="selectsede" class="block text-sm font-semibold">Sede<span class="text-red-500">*</span></label>                                           
                                                    <select wire:model="selectsede" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm" >
                                                        <option value="">Seleccione una Sede</option>
                                                        @foreach($sedes as $sede)
                                                        <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">
                                                @if($selectsede)
                                                <label for="selectpespec" class="block text-sm font-semibold">Servicios<span class="text-red-500">*</span></label>                                           
                                                    <select wire:model="selectpespec" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm" >
                                                        <option value="">Seleccione un Servicio</option>
                                                        @if($pservicios)
                                                            @foreach($pservicios as $pservicio)
                                                            <option value="{{ $pservicio->id }}">{{ $pservicio->descripcion }}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="col-span-6 sm:col-span-3">        
                                                @if($selectpespec && !is_null($servicios) && count($servicios) > 0)  
                                                    <label for="espec" class="block text-sm font-semibold">Especialidades<span class="text-red-500">*</span></label>
                                                    <select wire:model="espec" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none sm:text-sm">
                                                        <option value="">Seleccione una especialidad</option>
                                                        @foreach($servicios as $servicio)
                                                        <option value="{{ $servicio->servcod }}">{{ $servicio->servnomb }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif       
                                                @error('espec') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="historia" class="block text-sm font-semibold text-gray-700">Historia Clinica<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model.defer="historia" class="
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100" id="historia">
                                                    @error('historia') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="ordenMedica" class="block text-sm font-semibold text-gray-700">Orden médica<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model="ordenMedica" class="
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100" id="ordenMedica">
                                                    @error('ordenMedica') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                                <div class="mb-3 w-96">
                                                    <label for="documento_identidad" class="block text-sm font-semibold text-gray-700">Documento de identidad<span class="text-red-500">*</span></label>
                                                    <input type="file" wire:model="pacdocid" class="
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100" id="documento_identidad">
                                                    @error('pacdocid') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>
                                            </div>
                                            @if(Auth::user()->eps <> 60)
                                                <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                                    <div class="mb-3 w-96">
                                                        <label for="autorizacion" class="block text-sm font-semibold text-gray-700">Autorizacion EPS</label>
                                                        <input type="file" wire:model="autorizacion" class="
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100" id="autorizacion">
                                                        @error('autorizacion') <span class="px-2 inline-flex text-ms leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-span-6 sm:col-span-3">
                                                    <label for="codigo_autorizacion" class="block text-sm font-semibold text-gray-700">Código Autorizacion EPS</label>
                                                    <input wire:model="codigo_autorizacion" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" type="text" id="codigo_autorizacion" placeholder="Si tienes un código de autorización, digítalo aquí">
                                                    @error('codigo_autorizacion') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                </div>

                                                @if($espec == 1 || $espec == 491 || $espec == 4)
                                                <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                                    <div class="mb-3 w-96">
                                                        <label for="soporte" class="block text-sm font-semibold text-gray-700">Soporte patología<span class="text-red-500">*</span></label>
                                                        <input type="file" wire:model="soporte_patologia" class="
                                                    file:mr-4 file:py-2 file:px-4
                                                    file:rounded-full file:border-0
                                                    file:text-sm file:font-semibold
                                                    file:bg-blue-50 file:text-blue-700
                                                    hover:file:bg-blue-100" id="soporte">
                                                        @error('soporte_patologia') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-span-6 sm:col-span-6">

                                                    <label for="obs" class="block w-full text-sm font-medium text-gray-700">Observación</label>
                                                    <textarea wire:model="observacion" id="obs" class="rounded-lg border border-solid w-full border-gray-300" rows="8"></textarea>
                                                    @error('observacion') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span> @enderror

                                                </div>
                                        </div>

                                        <div class="px-4 py-3 text-center sm:px-6">
                                            <button wire:click.prevent="agendar()" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                                                Enviar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:load', function() {
            $('#select2').select2();
            $('#select2').on('change', function() {
                @this.set('espec', this.value);
            });
        });
    </script>
</div>
