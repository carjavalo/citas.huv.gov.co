<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight">
            {{ __('Solicitudes pendientes') }}
        </h2>
    </x-slot>
    @if ($modal)
    <div>
        @include('livewire.citas.cita-agendada')
    </div>
    @endif
    @if ($detalles)
    <div>
        @livewire('citas.detalle-cita', ['solicitud_id' => $sol_id])
    </div>
    @endif

    @if($rechazar)
    <div>
        @livewire('citas.rechazar-cita', ['solicitud_id' => $sol_id])
    </div>
    @endif

    @if($notificar_espera)
    <div>
        @livewire('citas.notificar-espera', ['solicitud_id' => $sol_id])
    </div>
    @endif
    <div wire:poll.visible>
        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- This example requires Tailwind CSS v2.0+ -->
                    <div class="grid grid-cols-6 gap-6 mx-2 my-2">
                        <div class="col-span-6 sm:col-span-1">
                            <label for="ubicacion" class="block text-sm text-center font-medium  text-gray-700">Filtrar por estado</label>
                            <select wire:model="filestado" type="text" id="ubicacion" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Espera">Espera</option>
                                <option value="Procesando">En proceso</option>
                                <option value="Rechazada">Rechazado</option>
                                <option value="Agendado">Agendado</option>
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="ubicacion" class="block text-sm text-center font-medium text-gray-700">Filtrar por especialidad</label>
                            <input type="text" wire:model="filserv" id="ubicacion" autocomplete="off" placeholder="Nombre del servicio" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="ubicacion" class="block text-sm text-center font-medium text-gray-700">Filtrar por usuario</label>
                            <input type="text" wire:model="filpaciente" id="ubicacion" autocomplete="off" placeholder="Número de documento" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="ubicacion" class="block text-sm text-center font-medium text-gray-700">Filtrar por eps</label>
                            <input type="text" wire:model="fileps" id="ubicacion" autocomplete="off" placeholder="Nombre del eps" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    ID
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Paciente
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Servicio
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Estado
                                                </th>
                                                <!-- <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Archivos
                                                </th> -->
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Fecha solicitud
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Acciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @if(count($solicitudes)>0)
                                            @foreach ($solicitudes as $solicitud)
                                            <tr>
                                                <td>
                                                    <p class="text-xs text-center">{{ $solicitud->id }}</p>
                                                </td>
                                                <td class="px-6 py-3 whitespace-nowrap ">
                                                    <div class="block text-center justify-center">
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $solicitud->name }} {{ $solicitud->apellido1 }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                {{ $solicitud->nombre }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">{{ $solicitud->servnomb }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-200 text-yellow-800">
                                                        {{ $solicitud->estado }}
                                                    </span>
                                                </td>
                                                <!-- <td class="px-6 py-4 whitespace-nowrap">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($solicitud->pachis) }}">Historia</a>
                                                            </span>
                                                            @if($solicitud->pacauto<>null)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($solicitud->pacauto) }}">Autorizacion</a>
                                                            </span>
                                                            @endif
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($solicitud->pacordmed) }}">Orden Médica</a>
                                                            </span>
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($solicitud->pacdocid) }}">Documento</a>
                                                            </span>
                                                        </td> -->
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        {{ $solicitud->created_at }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                                    @switch($solicitud->estado)
                                                        @case('Pendiente')
                                                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="agendar({{ $solicitud->id }})">Agendar</button>
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="notificarEspera({{ $solicitud->id }})">Espera</button>
                                                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded mx-1" wire:click="rechazar({{ $solicitud->id }})">Rechazar</button>                                                                                                                         
                                                        <button class="bg-gray-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded mx-1" wire:click="notificarEspera({{ $solicitud->id }})"   >Cambiar Esp</button> 
                                                         

                                                       @break

                                                        @case('Agendado')
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="cancelarCita({{ $solicitud->id }})" onclick="confirm('¿Está seguro de cancelar esta cita?') || event.stopImmediatePropagation()">Cancelar</button>
                                                            @break
                                                        
                                                        @case('Procesando')
                                                        <button class="bg-yellow-200 hover:bg-yellow-300 font-bold py-2 px-4 rounded mx-1" wire:click="cambiarEstado({{ $solicitud->id }})">Estado anterior</button>
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                            @break

                                                        @case('Espera')
                                                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="agendar({{ $solicitud->id }})">Agendar</button>
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        <button class="bg-yellow-200 hover:bg-yellow-300 font-bold py-2 px-4 rounded mx-1" wire:click="reagendarCita({{ $solicitud->id }})" onclick="confirm('¿Está seguro de reagendar a pendiente esta cita?') || event.stopImmediatePropagation()">Regresar pendiente</button>                                                                                                         
                                                      <!--<button class="bg-gray-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded mx-1" wire:click="notificarEspera({{ $solicitud->id }})">Cambiar Esp</button> -->  
                                                            @break
                                                        @case('Rechazada')
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                            @break
                                                    @endswitch
                                                        <!-- <button class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 ml-2 rounded">Notificar</button> -->
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="6">
                                                    <p class="text-center text text-lg underline">Sin solicitudes por procesar</p>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ $solicitudes->links() }}
                </div>
            </div>
        </div>
    </div>
</div>