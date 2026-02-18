<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
    <div class="flex justify-center min-h-max pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        <div class="fixed inset-0 trasition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-x1 transform trasition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
 
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Detalles de la cita</h3>
                        <p class="mt-1 max-w-2xl text-base text-gray-500">Datos del paciente.</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Paciente</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->paciente }} {{ $datos->apellido1 }} {{ $datos->apellido2 }}</dd>
                                <dt class="text-sm font-medium text-gray-500">{{ $datos->tipo_documento }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->numero_documento }}</dd>
                                <dt class="text-sm font-medium text-gray-500">Correo</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->email }}</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Especialidad solicitada</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->servnomb }}</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Solicitó el día</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->created_at }}</dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                @switch($datos->estado)
                                    @case('Agendado')
                                    <dt class="text-sm font-medium text-gray-500">Procesado por</dt>
                                    @if($agente)
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $agente->name }} {{ $agente->apellido1 }} </dd>
                                        <dt class="text-sm font-medium text-gray-500">Agendado el día</dt>
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->updated_at }} </dd>
                                    @else
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> Sin registro </dd>
                                    @endif
                                        @break
                                    
                                    @case('Procesando')
                                    <dt class="text-sm font-medium text-gray-500">Agente procesando</dt>
                                    @if($agente)
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $agente->name }} {{ $agente->apellido1 }} </dd>
                                    @else
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> Sin registro </dd>
                                    @endif
                                    <dt class="text-sm font-medium text-gray-500"></dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->updated_at }} </dd>
                                        @break

                                    @case('Espera')
                                    <dt class="text-sm font-medium text-gray-500">Actualizado por</dt>
                                    @if($agente)
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $agente->name }} {{ $agente->apellido1 }} </dd>
                                    @else
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> Sin registro </dd>
                                    @endif
                                    <dt class="text-sm font-medium text-gray-500">Fecha actualizacion</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->updated_at }} </dd>  
                                    <dt class="text-sm font-medium text-gray-500">Motivo de espera</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->motivo_espera }} </dd>  
                                        @break

                                    @case('Rechazada')
                                    <dt class="text-sm font-medium text-gray-500">Rechazado por</dt>
                                    @if($agente)
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $agente->name }} {{ $agente->apellido1 }} </dd>
                                    @else
                                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> Sin registro </dd>
                                    @endif
                                    <dt class="text-sm font-medium text-gray-500">Fecha de rechazo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->updated_at }} </dd>
                                    <dt class="text-sm font-medium text-gray-500">Motivo de rechazo</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"> {{ $datos->motivo_rechazo }} </dd>
                                        @break
                                @endswitch
                            </div>
                            @if($datos->pacobs)
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Observaciones</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ $datos->pacobs }}</dd>
                            </div>
                            @endif
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Adjuntos</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                        @if($datos->estado == 'Agendado')
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm bg-blue-50">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Desprendible cita </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                @if($datos->certfdo_cita <> null)
                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($datos->certfdo_cita) }}" class="font-medium text-blue-600 hover:text-blue-500"> Ver </a>
                                                @else
                                                <p>Pendiente por ruta</p>
                                                @endif
                                            </div>
                                        </li>
                                        @endif
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Historia clínica </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a href="{{ \App\Http\Controllers\DocumentoController::generarUrl($datos->pachis) }}" class="font-medium text-blue-600 hover:text-blue-500"> Ver </a>
                                            </div>
                                        </li>
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Documento identidad </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($datos->pacdocid) }}" class="font-medium text-blue-600 hover:text-blue-500"> Ver </a>
                                            </div>
                                        </li>
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Autorización </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                @if ($datos->pacauto <> null)
                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($datos->pacauto) }}" class="font-medium text-blue-600 hover:text-blue-500"> Ver </a>
                                                @else
                                                <p>Sin adjunto</p>
                                                @endif
                                            </div>
                                        </li>
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Orden </span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                @if ($datos->pacordmed <> null)
                                                <a target="_blank" href="{{ \App\Http\Controllers\DocumentoController::generarUrl($datos->pacordmed) }}" class="font-medium text-blue-600 hover:text-blue-500"> Ver </a>
                                                @else
                                                <p>Sin adjunto</p>
                                                @endif
                                            </div>
                                        </li>
                                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                            <div class="w-0 flex-1 flex items-center">
                                                <!-- Heroicon name: solid/paper-clip -->
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="ml-2 flex-1 w-0 truncate"> Código de autorización</span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                @if($datos->codigo_autorizacion <> null)
                                                <p>{{ $datos->codigo_autorizacion }}</p>
                                                @else
                                                <p>Sin datos</p>
                                                @endif
                                            </div>
                                        </li>
                                    </ul>
                                </dd>
                                <button class="justify-end bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click.prevent="$emitTo('citas.consulta-general','cerrarDetalles')">Cerrar</button>
                            </div>
                        </dl>
                    </div>
                </div>
            
        </div>
    </div>
</div>