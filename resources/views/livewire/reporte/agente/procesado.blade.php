<div>
    <style>
        html,
        body {
            height: 100%;
        }

        @media (min-width: 640px) {
            table {
                display: inline-table !important;
            }

            thead tr:not(:first-child) {
                display: none;
            }
        }

        td:not(:last-child) {
            border-bottom: 0;
        }

        th:not(:last-child) {
            border-bottom: 2px solid rgba(0, 0, 0, .1);
        }
    </style>
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <body class="flex items-center justify-center">
                        <div class="flex justify-center">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-2">
                                   <label for="desde">Filtrar desde</label>
                                    <input class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" type="date" wire:model="fecha_desde" id="desde" max="{{ $hoy }}">
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <label for="hasta">Filtrar hasta</label>
                                    <input class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" type="date" wire:model="fecha_hasta" id="hasta" max="{{ $hoy }}">
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    @if($filtro_ok)
                                    <button class="bg-green-600 hover:bg-green-700 block mt-7 text-white font-bold py-2 px-4 rounded mx-1" wire:click="exportar()">Exportar reporte</button>
                                    @else
                                    <button class="bg-blue-600 hover:bg-blue-700 block mt-7 text-white font-bold py-2 px-4 rounded mx-1" wire:click="filtroRangoFecha()">Aplicar filtro</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <table class="w-full flex flex-row flex-no-wrap sm:bg-white rounded-lg overflow-hidden sm:shadow-lg my-5">
                            <thead class="text-white">
                                <tr class="bg-blue-500 flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                                    <th class="p-3 text-center">ID</th>
                                    <th class="p-3 text-center">Agente</th>
                                    <th class="p-3 text-center">Agendado</th>
                                    <th class="p-3 text-center">Espera</th>
                                    <th class="p-3 text-center">Rechazado</th>
                                </tr>
                            </thead>
                            <tbody class="flex-1 sm:flex-none">
                                @foreach($agentes as $agente)
                                <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3">{{ $agente->id }}</td>
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3">{{ $agente->name }} {{ $agente->apellido1 }} {{ $agente->apellido2 }}</td>
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3 truncate">{{ count($solicitudes->where('usercod','=',$agente->id)->where('estado','=','Agendado')) }}</td>
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3 truncate">{{ count($solicitudes->where('usercod','=',$agente->id)->where('estado','=','Espera')) }}</td>
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3 truncate">{{ count($solicitudes->where('usercod','=',$agente->id)->where('estado','=','Rechazada')) }}</td>
                                </tr>
                                @endforeach
                                <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                                    <td colspan="4" class="text-right">Total:</td>
                                    <td class="text-center border-grey-light border hover:bg-gray-100 p-3">{{ count($solicitudes) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex justify-end">
                            @if(!$filtro_ok)
                            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="exportarHoy()">Exportar hoy</button>
                            @endif
                        </div>
                </body>

            </div>
        </div>
    </div>
</div>