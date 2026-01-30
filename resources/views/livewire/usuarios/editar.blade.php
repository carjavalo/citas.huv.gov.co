<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
    <div class="flex justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="fixed inset-0 trasition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-x1 transform trasition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4 text-center">
                        <label class="text-2xl" for="">Edición de usuarios</label>
                    </div>
                    <div class="rounded-lg grid grid-cols-6 gap-6 mb-4">
                        <div class="col-span-6 sm:col-span-3">
                            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input wire:model="nombres" type="text" id="nombres" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="apellido1" class="block text-sm font-medium text-gray-700">Primer apellido</label>
                            <input wire:model="apellido1" type="text" id="apellido1" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="apellido2" class="block text-sm font-medium text-gray-700">Segundo apellido</label>
                            <input wire:model="apellido2" type="text" id="apellido2" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo a notificar</label>
                            <input wire:model="email" type="text" id="email" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="rol" class="block text-sm font-medium text-gray-700">Rol<span class="text-red-500">*</span></label>
                            <select wire:model="usu_rol" id="rol" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach($roles as $role)
                                    @if($role->name == $usu_rol)
                                        <option value="{{ $role->name}}" selected>{{ $role->name }}</option>
                                    @else
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('rol') <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>  @enderror
                        </div>

                        <div class="col-span-6 sm:col-span-3">
                            <label for="eps" class="block text-sm font-medium text-gray-700">EPS</label>
                            <select  wire:model.defer="usu_eps" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach ($aseguradoras as $eps)
                                        @if ($eps->id == $usu_eps )
                                            <option value="{{ $eps->id }}" selected>{{ $eps->nombre }}</option>
                                        @else
                                            <option value="{{ $eps->id }}">{{ $eps->nombre }}</option>
                                        @endif
                                    @endforeach
                            </select>
                        </div>
                        
                        {{-- Campos condicionales para roles Consultor, Coordinador y Administrador --}}
                        @if(in_array($usu_rol, ['Consultor', 'Coordinador', 'Administrador']))
                        <div class="col-span-6 sm:col-span-3">
                            <label for="sede" class="block text-sm font-medium text-gray-700">Sede<span class="text-red-500">*</span></label>
                            <select wire:model="usu_sede" id="sede" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Seleccione una sede</option>
                                @foreach ($sedes as $sede)
                                    <option value="{{ $sede->id }}" {{ $usu_sede == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="pservicio" class="block text-sm font-medium text-gray-700">Servicio<span class="text-red-500">*</span></label>
                            <select wire:model="usu_pservicio" id="pservicio" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Seleccione un servicio</option>
                                @foreach ($pservicios as $pservicio)
                                    <option value="{{ $pservicio->id }}" {{ $usu_pservicio == $pservicio->id ? 'selected' : '' }}>{{ $pservicio->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        
                        <div class="col-span-6 sm:col-span-3">
                            <label for="tipo_documento" class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                            <select wire:model="tdoc" id="tipo_documento" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($tipos_identificacion as $tipo_identificacion)
                                    @if ($tipo_identificacion->id == $tdoc)
                                        <option value="{{ $tipo_identificacion->id }}" selected>{{ $tipo_identificacion->nombre }}</option>
                                    @else
                                        <option value="{{ $tipo_identificacion->id }}">{{ $tipo_identificacion->nombre }}</option>
                                     @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label for="numero_doucumento" class="block text-sm font-medium text-gray-700">Número de documento</label>
                            <input wire:model="ndoc" type="text" id="numero_documento" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <div class="block col-span-6 sm:col-span-3 justify-center">
                                <button wire:click.prevent="actualizar()" class="w-full justify-center transition ease-in-out delay-150 bg-green-600 hover:-translate-y-1 hover:scale-110 hover:bg-green-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                    Actualizar
                                </button>
                            </div>   
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <div class="block col-span-6 sm:col-span-3 justify-center">
                                <button wire:click="cerrarModal()" type="button" class="w-full justify-center transition ease-in-out delay-150 bg-red-600 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>