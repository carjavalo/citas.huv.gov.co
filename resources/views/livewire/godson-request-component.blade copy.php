<div wire:init="loadRequests">
    <div>
        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="grid grid-cols-6 gap-6 mx-2 my-2">
                        @can('godson.request.make')
                            <div class="col-span-6 sm:col-span-1">
                                <button class="bg-blue-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mx-1"
                                    type="button" wire:click="createRequest()">
                                    Solicitar
                                </button>
                            </div>
                        @endcan
                        @can('godson.request.attend')
                            <div class="col-span-6 sm:col-span-1">
                                <label class="block text-sm text-center font-medium  text-gray-700">Filtrar
                                    por Hospital</label>
                                <select wire:model="selectedHospital" type="text" autocomplete="off"
                                    class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Seleccione...</option>
                                    @foreach ($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}">{{ $hospital->descripcion }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endcan
                        <div class="col-span-6 sm:col-span-1">
                            <label class="block text-sm text-center font-medium  text-gray-700">Filtrar
                                por estado</label>
                            <select wire:model="selectedStatus" type="text" autocomplete="off"
                                class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option>Todos</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="ubicacion" class="block text-sm text-center font-medium text-gray-700">Filtrar
                                por paciente</label>
                            <input type="text" wire:model.debounce.1000ms="pacient" autocomplete="off"
                                class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    # Documento
                                                </th>
                                                @if (!$isHospital)
                                                    <th scope="col"
                                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Hospital
                                                    </th>
                                                @endif
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Paciente
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Especialidad
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Fecha solicitud
                                                </th>
                                                <th scope="col"
                                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if (count($solicitudes))
                                                @foreach ($solicitudes as $item)
                                                    <tr>
                                                        <td>
                                                            <p class="text-xs text-center">
                                                                {{ $item->paciente->numero_documento }}</p>
                                                        </td>
                                                        @if (!$isHospital)
                                                            <td>
                                                                <p class="text-xs text-center">
                                                                    {{ $item->hospital->descripcion }}</p>
                                                            </td>
                                                        @endif
                                                        <td class="px-6 py-3 whitespace-nowrap ">
                                                            <div class="block text-center justify-center">
                                                                <div class="ml-4">
                                                                    <div class="text-sm font-medium text-gray-900">
                                                                        {{ $item->paciente->primer_nombre . ' ' . $item->paciente->primer_apellido }}
                                                                    </div>
                                                                    <div class="text-sm text-gray-500">
                                                                        {{ $item->paciente->eps->nombre }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <div class="text-sm text-gray-900">
                                                                {{ $item->especialidad->servnomb }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            @if ($item->estado_solicitud_id == 1) bg-yellow-200 @endif
                                                            @if ($item->estado_solicitud_id == 2) bg-blue-200 text-white-800 @endif
                                                            @if ($item->estado_solicitud_id == 3) bg-green-200 text-white-800 @endif
                                                            @if ($item->estado_solicitud_id == 4) bg-red-200 text-white-800 @endif">
                                                                {{ $item->estado->descripcion }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                                {{ $item->created_at }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                                            <span
                                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                                @can('godson.request.attend')
                                                                    @if ($item->estado_solicitud_id == 1 || $item->estado_solicitud_id == 2)
                                                                        <button
                                                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                            wire:click="attendRequestModal({{ $item->id }}, {{ $item->paciente_obstetrica_id }})">Atender
                                                                        </button>
                                                                        
                                                                    @endif
                                                                @endcan
                                                                @can('godson.request.check')
                                                                    @if ($item->estado_solicitud_id == 1)
                                                                        <button
                                                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                            wire:click="attendRequestModal({{ $item->id }}, {{ $item->paciente_obstetrica_id }},true)">Revisión
                                                                        </button>
                                                                    @endif
                                                                @endcan
                                                                @if ($item->estado_solicitud_id != 1)
                                                                    <button
                                                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                        wire:click="detailsRequestModal({{ $item->id }}, {{ $item->paciente_obstetrica_id }})">Detalles
                                                                    </button>
                                                                    
                                                                                                                                    
                                                                @endif
                                                                @can('godson.request.reject')
                                                                    @if ($item->estado_solicitud_id == 1)
                                                                        <button
                                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                            wire:click="attendRequestModal({{ $item->id }}, {{ $item->paciente_obstetrica_id }},false,true)">Rechazar
                                                                        </button>
                                                                    @endif
                                                                @endcan
                                                                @can('godson.request.cancel')
                                                                    @if ($item->estado_solicitud_id == 1 && $isHospital)
                                                                        <button
                                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                            wire:click="cancelRequest({{ $item->id }}, {{ $item->paciente_obstetrica_id }})">Cancelar
                                                                        </button>
                                                                        
                                                                        <button
                                                                          class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1"
                                                                          wire:click="detailsRequestModal({{ $item->id }}, {{ $item->paciente_obstetrica_id }})">Modificar
                                                                        </button>                                                                                                                                                                                                      
                                                                    @endif
                                                                @endcan
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7">
                                                        <p class="text-center text text-lg underline">Sin solicitudes
                                                            por procesar</p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- {{ $solicitudes->links() }} --}}
                </div>
            </div>
        </div>
    </div>
    @if ($createRequest)
        <x-jet-dialog-modal wire:model="createRequest">
            <x-slot name="title">
                Registrar nueva solicitud
            </x-slot>

            <x-slot name="content">
                <div
                    class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-blueGray-100 border-0">
                    <h6 class="text-blueGray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Información del paciente
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Primer nombre
                                </label>
                                <input type="text"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.primer_nombre">
                                @error('paciente.primer_nombre')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Segundo nombre
                                </label>
                                <input type="email"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.segundo_nombre">
                                @error('paciente.segundo_nombre')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Primer apellido
                                </label>
                                <input type="text"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.primer_apellido">
                                @error('paciente.primer_apellido')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Segundo apellido
                                </label>
                                <input type="text"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.segundo_apellido">
                                @error('paciente.segundo_apellido')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Fecha de nacimiento
                                </label>
                                <input type="date"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.fecha_nacimiento">
                                @error('paciente.fecha_nacimiento')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Tipo de documento
                                </label>
                                <select wire:model="paciente.tipo_documento_id"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150">
                                    <option>Seleccione</option>
                                    @foreach ($tiposIdentificacion as $tipoIdentificacion)
                                        <option value="{{ $tipoIdentificacion['id'] }}">
                                            {{ $tipoIdentificacion['nombre'] }}</option>
                                    @endforeach
                                </select> @error('paciente.tipo_documento_id')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Número de documento
                                </label>
                                <input type="number"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.numero_documento">
                                @error('paciente.numero_documento')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    EPS
                                </label>
                                <select wire:model="paciente.eps_id"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150">
                                   <option value="">Seleccione Eps</option>
                                    @foreach ($eps as $entidad)
                                        <option value="{{ $entidad['id'] }}">{{ $entidad['nombre'] }}</option>
                                    @endforeach
                                </select> @error('paciente.eps_id')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Dirección de residencia
                                </label>
                                <input type="text"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.direccion_residencia">
                                @error('paciente.direccion_residencia')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-6/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Teléfono
                                </label>
                                <input type="number"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    wire:model="paciente.telefono">
                                @error('paciente.telefono')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="mt-6 border-b-1 border-blueGray-300">

                    <h6 class="text-blueGray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Información clínica
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-12/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Especialidad
                                </label>
                                <select wire:model="solicitud.especialidad_id"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150">
                                    <option>Seleccione</option>
                                    @foreach ($especialidades as $especialidad)
                                        <option selected value="{{ $especialidad['id'] }}">{{ $especialidad['servnomb'] }}</option>
                                    @endforeach
                                </select>


                            

                            </div>
                        </div>
                        <div class="w-full lg:w-4/12 px-4">
                            <div class="mb-3 w-96">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Historia clínica
                                </label>
                                <input type="file" wire:model="solicitud.historia_clinica"
                                    class="
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                                @error('solicitud.historia_clinica')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-4/12 px-4">
                            <div class="mb-3 w-96">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Documento de identidad
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="file" wire:model.defer="solicitud.documento_identidad"
                                    class="
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                                @error('solicitud.documento_identidad')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="w-full lg:w-4/12 px-4">
                            <div class="mb-3 w-96">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Adjunto adicional
                                </label>
                                <input type="file" wire:model.defer="solicitud.adjunto_adicional"
                                    class="
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                                @error('solicitud.adjunto_adicional')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="mt-6 border-b-1 border-blueGray-300">

                    <h6 class="text-blueGray-400 text-sm mt-3 mb-6 font-bold uppercase">
                        Información adicional
                    </h6>
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-12/12 px-4">
                            <div class="relative w-full mb-3">
                                <label class="block uppercase text-blueGray-600 text-xs font-bold mb-2">
                                    Observaciones
                                </label>
                                <textarea type="text" wire:model="solicitud.observacion"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    rows="4"> </textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <footer class="relative  pt-8 pb-6 mt-2">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap items-center md:justify-between justify-center">
                        <div class="w-full md:w-6/12 px-4 mx-auto text-center">
                            <div class="text-sm text-blueGray-500 font-semibold py-1">
                                Posible texto
                            </div>
                        </div>
                    </div>
                </div>
            </footer> -->
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('createRequest')" wire:loading.attr="disabled">
                    Cancelar
                </x-jet-secondary-button>

                <x-jet-secondary-button class="ml-2" wire:click="registrarSolicitud" wire:loading.attr="disabled"
                    wire:target="registrarSolicitud">
                    Registrar solicitud
                </x-jet-secondary-button>
            </x-slot>
        </x-jet-dialog-modal>
    @endif
    @if ($modalAttendRequest && $paciente)
        <x-jet-dialog-modal wire:model="modalAttendRequest">
            <x-slot name="title">
                Atender solicitud
            </x-slot>

            <x-slot name="content">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="bg-blue-100 rounded-lg mb-4 text-center">
                        <label class="text-2xl" for="">Información de la solicitud</label>
                    </div>
                    <div class="bg-gray-100 rounded-lg grid grid-cols-6 gap-6 mb-2 select-none">
                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label for="">Paciente:</label>
                            <p>{{ $paciente->primer_nombre }} {{ $paciente->segundo_nombre }}
                                {{ $paciente->primer_apellido }} {{ $paciente->segundo_apellido }} </p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Contacto:</label>
                            <p>{{ $paciente->telefono ?? 'No reporta' }}</p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Tipo de documento:</label>
                            <p>{{ $paciente->tipo_documento->nombre }}</p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Numero de documento:</label>
                            <p>{{ $paciente->numero_documento }}</p>
                        </div>

                        <div class="col-span-6 mx-2 mb-2">
                            <label for="">Documentos:</label>
                            @if ($solicitud->historia_clinica)
                                <button wire:click="downloadFile('{{ "$solicitud->historia_clinica" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Historia clínica
                                </button>
                            @endif
                            @if ($solicitud->documento_identidad)
                                <button wire:click="downloadFile('{{ "$solicitud->documento_identidad" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Documento de identidad
                                </button>
                            @endif
                            @if ($solicitud->adjunto_adicional)
                                <button wire:click="downloadFile('{{ "$solicitud->adjunto_adicional" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Adjunto adicional
                                </button>
                            @endif
                        </div>
                        <div class="col-span-6 sm:col-span-6 mx-1 sm:row-span-1 bg-white mb-2 rounded-lg">
                            <label>Observación</label>
                            <p class="mx-2 text-justify">{{ $solicitud->observacion }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-6 gap-6">
                        @if (!$toRevisionRequest && !$toRejectRequest)
                            <div class="col-span-6 sm:col-span-3">
                                <label for="medico" class="block text-sm font-medium text-gray-700">Historia
                                    clínica</label>
                                <input wire:model="adjunto.0" type="file" id="certificado" autocomplete="off"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                                @error('adjunto.0')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"></span>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="medico" class="block text-sm font-medium text-gray-700">Adjunto
                                    1</label>
                                <input wire:model="adjunto.1" type="file" id="adjunto1" autocomplete="off"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                                @error('adjunto.1')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"></span>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="medico" class="block text-sm font-medium text-gray-700">Adjunto
                                    2</label>
                                <input wire:model="adjunto.2" type="file" id="adjunto2" autocomplete="off"
                                    class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                                @error('adjunto.2')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"></span>
                                @enderror
                            </div>
                        @endif
                        <div class="col-span-6 sm:col-span-3">
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo a
                                notificar</label>
                            <p>{{ $solicitud->hospital->email }}</p>
                        </div>

                        <div class="col-span-6 sm:col-span-6">

                            <label for="obs"
                                class="block w-full text-sm font-medium text-gray-700">Mensaje</label>
                            <textarea wire:model="observacion" id="obs"
                                class="rounded-lg border border-solid w-full h-auto border-gray-300"></textarea>
                            @error('observacion')
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"></span>
                            @enderror

                        </div>

                    </div>
                </div>
                <!-- <footer class="relative  pt-8 pb-6 mt-2">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center md:justify-between justify-center">
                    <div class="w-full md:w-6/12 px-4 mx-auto text-center">
                        <div class="text-sm text-blueGray-500 font-semibold py-1">
                            Posible texto
                        </div>
                    </div>
                </div>
            </div>
        </footer> -->
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('modalAttendRequest')" wire:loading.attr="disabled">
                    Cancelar
                </x-jet-secondary-button>
                @if (!$toRevisionRequest && !$toRejectRequest)
                    <x-jet-secondary-button class="ml-2 bg-green-500 text-white" wire:click="attendRequest"
                        wire:loading.attr="disabled" wire:target="attendRequest">
                        Atender
                    </x-jet-secondary-button>
                @endif
                @if ($toRejectRequest)
                    <x-jet-secondary-button class="bg-red-500 text-white" wire:click="rejectRequest"
                        wire:loading.attr="disabled" wire:target="rejectRequest">
                        Rechazar
                    </x-jet-secondary-button>
                @endif
                @if ($toRevisionRequest)
                    <x-jet-secondary-button class="ml-2 bg-blue-500 text-white" wire:click="toRevisionRequest"
                        wire:loading.attr="disabled" wire:target="toRevisionRequest">
                        Actualizar
                    </x-jet-secondary-button>
                @endif
            </x-slot>
        </x-jet-dialog-modal>
    @endif
    @if ($modalDetailsRequest && $paciente)
        <x-jet-dialog-modal wire:model="modalDetailsRequest">
            <x-slot name="title">
                Atender solicitud
            </x-slot>

            <x-slot name="content">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="bg-blue-100 rounded-lg mb-4 text-center">
                        <label class="text-2xl" for="">Información de la solicitud</label>
                    </div>
                    <div class="bg-gray-100 rounded-lg grid grid-cols-6 gap-6 mb-2 select-none">
                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label for="">Paciente:</label>

                            
                            <p>{{ $paciente->primer_nombre }} {{ $paciente->segundo_nombre }}
                                {{ $paciente->primer_apellido }} {{ $paciente->segundo_apellido }} </p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Contacto:</label>
                            <p>{{ $paciente->telefono ?? 'No reporta' }}</p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Tipo de documento:</label>
                            <p>{{ $paciente->tipo_documento->nombre }}</p>
                        </div>

                        <div class="col-span-6 sm:col-span-3 mx-2">
                            <label>Numero de documento:</label>
                            <p>{{ $paciente->numero_documento }}</p>
                        </div>

                        <div class="col-span-6 mx-2 mb-2">
                            @if ($solicitud->historia_clinica)
                                <button wire:click="downloadFile('{{ "$solicitud->historia_clinica" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Historia clínica
                                </button>
                            @endif
                            @if ($solicitud->documento_identidad)
                                <button wire:click="downloadFile('{{ "$solicitud->documento_identidad" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Documento de identidad
                                </button>
                            @endif
                            @if ($solicitud->adjunto_adicional)
                                <button wire:click="downloadFile('{{ "$solicitud->adjunto_adicional" }}')"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Adjunto adicional
                                </button>
                            @endif
                        </div>
                        <div class="col-span-6 sm:col-span-6 mx-1 sm:row-span-1 bg-white mb-2 rounded-lg">
                            <label>Observación</label>
                            <p class="mx-2 text-justify">{{ $solicitud->observacion }}</p>
                        </div>
                    </div>
                </div>
                <!-- <footer class="relative  pt-8 pb-6 mt-2">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center md:justify-between justify-center">
                    <div class="w-full md:w-6/12 px-4 mx-auto text-center">
                        <div class="text-sm text-blueGray-500 font-semibold py-1">
                            Posible texto
                        </div>
                    </div>
                </div>
            </div>
        </footer> -->
            </x-slot>

            <x-slot name="footer">
                <x-jet-secondary-button wire:click="$toggle('modalDetailsRequest')" wire:loading.attr="disabled">
                    Cerrar
                </x-jet-secondary-button>
            </x-slot>
        </x-jet-dialog-modal>
    @endif



<!--____________________________prueba____________________________________________________________________________________-->




<!--____________________________prueba____________________________________________________________________________________-->

</div>
