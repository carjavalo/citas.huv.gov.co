<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-800 leading-tight">
            {{ __('Solicitudes') }}
        </h2>
    </x-slot>
    @if ($modal)
        @include('livewire.citas.cita-agendada')
    @endif
    @if ($detalles)
        @livewire('citas.detalle-cita', ['solicitud_id' => $sol_id])
    @endif

    @if($rechazar)
        @livewire('citas.rechazar-cita', ['solicitud_id' => $sol_id])
    @endif

    @if($notificar_espera)
        @livewire('citas.notificar-espera', ['solicitud_id' => $sol_id])
    @endif
    <div wire:poll.visible.60s>
        <div class="py-12">
            <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- This example requires Tailwind CSS v2.0+ -->
                    <div class="grid grid-cols-6 gap-6 mx-2 my-2">
                        @role('Super Admin')
                        <div class="col-span-6 flex items-center gap-4 mb-2">
                            <button onclick="if(confirm('¿Está seguro de eliminar las solicitudes seleccionadas? Esta acción no se puede deshacer.')) { @this.call('eliminarSeleccionados') }" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center gap-2" {{ count($selectedSolicitudes) == 0 ? 'disabled' : '' }}>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                Eliminar seleccionados ({{ count($selectedSolicitudes) }})
                            </button>
                        </div>
                        @endrole
                        <div class="col-span-6 sm:col-span-1">
                            <label for="filestado" class="block text-sm text-center font-medium text-gray-700">Filtrar por estado</label>
                            <select wire:model="filestado" id="filestado" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Espera">Espera</option>
                                <option value="Procesando">En proceso</option>
                                <option value="Rechazada">Rechazado</option>
                                <option value="Agendado">Agendado</option>
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="filserv" class="block text-sm text-center font-medium text-gray-700">Filtrar por especialidad</label>
                            <input type="text" wire:model="filserv" id="filserv" list="especialidades-list" autocomplete="off" placeholder="Nombre de la Especialidad" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <datalist id="especialidades-list">
                                @foreach($especialidades as $especialidad)
                                    <option value="{{ $especialidad->servnomb }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="filpaciente" class="block text-sm text-center font-medium text-gray-700">Filtrar por usuario</label>
                            <input type="text" wire:model="filpaciente" id="filpaciente" autocomplete="off" placeholder="Número de documento" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="fileps" class="block text-sm text-center font-medium text-gray-700">Filtrar por eps</label>
                            <input type="text" wire:model="fileps" id="fileps" list="eps-list" autocomplete="off" placeholder="Nombre de la EPS" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <datalist id="eps-list">
                                @foreach($aseguradoras as $aseguradora)
                                    <option value="{{ $aseguradora->nombre }}">
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-span-6 sm:col-span-1">
                            <label for="filsede" class="block text-sm text-center font-medium text-gray-700">Filtrar por sede</label>
                            <select wire:model="filsede" id="filsede" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                <option value="">Todas las sedes</option>
                                @foreach($sedes as $sede)
                                    <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                @role('Super Admin')
                                                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                </th>
                                                @endrole
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('solicitudes.id')">
                                                    <div class="flex items-center justify-center">
                                                        ID
                                                        @if($sortField === 'solicitudes.id')
                                                            @if($sortDirection === 'asc')
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                            @else
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                        @endif
                                                    </div>
                                                </th>
                                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('solicitudes.solnum')">
                                                        <div class="flex items-center justify-center">
                                                            Consecutivo
                                                            @if($sortField === 'solicitudes.solnum')
                                                                @if($sortDirection === 'asc')
                                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                                @else
                                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                                @endif
                                                            @else
                                                                <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                            @endif
                                                        </div>
                                                    </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('users.name')">
                                                    <div class="flex items-center justify-center">
                                                        Paciente
                                                        @if($sortField === 'users.name')
                                                            @if($sortDirection === 'asc')
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                            @else
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                        @endif
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('servicios.servnomb')">
                                                    <div class="flex items-center justify-center">
                                                        Servicio
                                                        @if($sortField === 'servicios.servnomb')
                                                            @if($sortDirection === 'asc')
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                            @else
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                        @endif
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('solicitudes.estado')">
                                                    <div class="flex items-center justify-center">
                                                        Estado
                                                        @if($sortField === 'solicitudes.estado')
                                                            @if($sortDirection === 'asc')
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                            @else
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                        @endif
                                                    </div>
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" wire:click="sortBy('solicitudes.created_at')">
                                                    <div class="flex items-center justify-center">
                                                        Fecha solicitud
                                                        @if($sortField === 'solicitudes.created_at')
                                                            @if($sortDirection === 'asc')
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                                            @else
                                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                                            @endif
                                                        @else
                                                            <svg class="w-4 h-4 ml-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg>
                                                        @endif
                                                    </div>
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
                                                @role('Super Admin')
                                                <td class="px-3 py-4 whitespace-nowrap text-center">
                                                    <input type="checkbox" value="{{ $solicitud->id }}" wire:model="selectedSolicitudes" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer w-5 h-5">
                                                </td>
                                                @endrole
                                                <td>
                                                    <p class="text-xs text-center">{{ $solicitud->id }}</p>
                                                </td>
                                                    <td>
                                                        <p class="text-xs text-center">{{ $solicitud->solnum }}</p>
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
                                                    @switch($solicitud->estado)
                                                        @case('Pendiente')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-200 text-yellow-800">Pendiente</span>
                                                            @break
                                                        @case('Espera')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-200 text-orange-800">Espera</span>
                                                            @break
                                                        @case('Procesando')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-200 text-blue-800">En proceso</span>
                                                            @break
                                                        @case('Rechazada')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-200 text-red-800">Rechazado</span>
                                                            @break
                                                        @case('Agendado')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-200 text-green-800">Agendado</span>
                                                            @break
                                                        @default
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-800">{{ $solicitud->estado }}</span>
                                                    @endswitch
                                                </td>
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
                                                        <button class="bg-gray-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded mx-1" wire:click="notificarEspera({{ $solicitud->id }})" onclick="cargarespecialidades()">Cambiar Esp</button>
                                                        @role('Super Admin')
                                                        <button class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-2 rounded mx-1" onclick="if(confirm('¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.')) { @this.call('eliminarSolicitud', {{ $solicitud->id }}) }" title="Eliminar">🗑️</button>
                                                        @endrole
                                                       @break

                                                        @case('Agendado')
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="cancelarCita({{ $solicitud->id }})" onclick="confirm('¿Está seguro de cancelar esta cita?') || event.stopImmediatePropagation()">Cancelar</button>
                                                        @role('Super Admin')
                                                        <button class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-2 rounded mx-1" onclick="if(confirm('¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.')) { @this.call('eliminarSolicitud', {{ $solicitud->id }}) }" title="Eliminar">🗑️</button>
                                                        @endrole
                                                            @break
                                                        
                                                        @case('Procesando')
                                                        <button class="bg-yellow-200 hover:bg-yellow-300 font-bold py-2 px-4 rounded mx-1" wire:click="cambiarEstado({{ $solicitud->id }})">Estado anterior</button>
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        @role('Super Admin')
                                                        <button class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-2 rounded mx-1" onclick="if(confirm('¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.')) { @this.call('eliminarSolicitud', {{ $solicitud->id }}) }" title="Eliminar">🗑️</button>
                                                        @endrole
                                                            @break

                                                        @case('Espera')
                                                        <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="agendar({{ $solicitud->id }})">Agendar</button>
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        <button class="bg-yellow-200 hover:bg-yellow-300 font-bold py-2 px-4 rounded mx-1" wire:click="reagendarCita({{ $solicitud->id }})" onclick="confirm('¿Está seguro de reagendar a pendiente esta cita?') || event.stopImmediatePropagation()">Regresar pendiente</button>                                                                                                         
                                                        <button class="bg-gray-600 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded mx-1" wire:click="notificarEspera({{ $solicitud->id }})" onclick="cargarespecialidades()">Cambiar Esp</button>
                                                        @role('Super Admin')
                                                        <button class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-2 rounded mx-1" onclick="if(confirm('¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.')) { @this.call('eliminarSolicitud', {{ $solicitud->id }}) }" title="Eliminar">🗑️</button>
                                                        @endrole
                                                            @break
                                                        @case('Rechazada')
                                                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="detalles({{ $solicitud->id }})">Detalles</button>
                                                        @role('Super Admin')
                                                        <button class="bg-red-800 hover:bg-red-900 text-white font-bold py-2 px-2 rounded mx-1" onclick="if(confirm('¿Está seguro de eliminar esta solicitud? Esta acción no se puede deshacer.')) { @this.call('eliminarSolicitud', {{ $solicitud->id }}) }" title="Eliminar">🗑️</button>
                                                        @endrole
                                                            @break
                                                    @endswitch
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="@role('Super Admin') 7 @else 6 @endrole">
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

    <script>
        //Para emular click del usuario
        function cargarespecialidades(){
            setTimeout(function(){
                document.getElementById("test111").click();
            }, 1000);
        }
    </script>
</div>