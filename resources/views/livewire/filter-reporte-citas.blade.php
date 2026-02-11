    
    <div  class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
     
        <!--Filtros--> 
        <div class="bg-white rounded py-3 shadow mb-4 px-6">
            <h2 class="text-2x1 font-semibold mb-4">Generar Reportes</h2>
            <div class="mb-4">
               <!--Estado:   
                <select wire:model="filters.estado" name="estado" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"> 
                    <option value="">Todas</option>
                    <option value="Espera">Esperas</option>
                    <option value="Pendiente">Pendiente</option>
                </select>-->
                
            </div>
            <div class="flex flex-wrap gap-4 mb-4">
                <div>
                    Filtrar desde:
                    <input wire:model="filters.fromDate" type="date" class="w-36 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
                    
                </div>
                <div> 
                    Filtrar hasta:
                    <input wire:model="filters.toDate" type="date" class="w-36 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"/>
                </div>

                @role('Super Admin')
                <div>
                    Sede:
                    <select wire:model="filSede" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todas las sedes</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    Servicio:
                    <select wire:model="filServicio" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todos los servicios</option>
                        @foreach($pservicios as $pservicio)
                            <option value="{{ $pservicio->id }}">{{ $pservicio->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    Especialidad:
                    <select wire:model="filEspecialidad" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todas las especialidades</option>
                        @foreach($especialidades as $especialidad)
                            <option value="{{ $especialidad->servcod }}">{{ $especialidad->servnomb }}</option>
                        @endforeach
                    </select>
                </div>
                @endrole
            </div>
              <x-jet-button wire:click="generateReport">
                Generar Reporte
              </x-jet-button>
        </div>

      
        <!--Tabla-->
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Codigo Especialidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Servicio
                            </th>
                            <th scope="col" class="px-6 py-3">
                            Estado
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Fecha Solicitud
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Fecha Tramite
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Nombre Paciente
                            </th>
                            <th scope="col" class="px-6 py-3">
                                APellido
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Apellido
                            </th>
                            <th scope="col" class="px-6 py-3">
                                N. Documento
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Codigo Eps
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Eps
                            </th>
                        </tr>
                    </thead>
               <tbody>
                    @foreach($solicitudes as $solicitude)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                      {{$solicitude->espec}}
                            </th>
                            <td class="px-6 py-4">
                                   {{$solicitude->servicio_nombre}}
                            </td>
                            <td class="px-6 py-4">
                                     {{$solicitude->estado}}
                            </td>
                            <td class="px-6 py-4">
                                      {{$solicitude->created_at->format('d/m/y')}}
                            </td>
                            <td class="px-6 py-4">
                                {{$solicitude->updated_at->format('d/m/y')}}
                            </td>
                            <td class="px-6 py-4">
                                      {{$solicitude->paciente_nombre}}
                            </td>
                            <td class="px-6 py-4">
                                       {{$solicitude->paciente_apellido1}}
                            </td>
                            <td class="px-6 py-4">
                                       {{$solicitude->paciente_apellido2}}
                            </td>
                            <td class="px-6 py-4">
                                       {{$solicitude->paciente_ndocumento}}
                            </td>
                            <td class="px-6 py-4">
                                        {{$solicitude->codigo_eps}}
                            </td>
                            <td class="px-6 py-4">
                                    {{$solicitude->eps}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
                <div class="mt-4">
                    {{$solicitudes->links()}}
                </div>
    </div>  
   