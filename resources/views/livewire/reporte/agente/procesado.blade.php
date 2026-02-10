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

        .badge-agendado {
            background-color: #10B981;
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        .badge-espera {
            background-color: #F59E0B;
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        .badge-rechazada {
            background-color: #EF4444;
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        .detail-row {
            background-color: #f9fafb;
        }
    </style>
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                {{-- Filtros de fecha --}}
                <div class="flex justify-center mb-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-2">
                           <label for="desde" class="block text-sm font-medium text-gray-700">Filtrar desde</label>
                            <input class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" type="date" wire:model="fecha_desde" id="desde" max="{{ $hoy }}">
                        </div>
                        <div class="col-span-6 sm:col-span-2">
                            <label for="hasta" class="block text-sm font-medium text-gray-700">Filtrar hasta</label>
                            <input class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" type="date" wire:model="fecha_hasta" id="hasta" max="{{ $hoy }}">
                        </div>
                        <div class="col-span-6 sm:col-span-2">
                            <div class="flex gap-2 mt-6">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" wire:click="filtroRangoFecha()">Aplicar filtro</button>
                                @if($filtro_ok)
                                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" wire:click="exportar()">Exportar reporte</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Resumen por agente --}}
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Resumen por Colaborador (Consultor)</h3>
                <table class="w-full bg-white rounded-lg overflow-hidden shadow-lg mb-6">
                    <thead class="text-white">
                        <tr class="bg-blue-500">
                            <th class="p-3 text-center">ID</th>
                            <th class="p-3 text-center">Agente</th>
                            <th class="p-3 text-center">Agendado</th>
                            <th class="p-3 text-center">Espera</th>
                            <th class="p-3 text-center">Rechazado</th>
                            <th class="p-3 text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agentes as $agente)
                        @php
                            $solicitudesAgente = $solicitudes->where('usercod','=',$agente->id);
                            $agendados = $solicitudesAgente->where('estado','=','Agendado')->count();
                            $espera = $solicitudesAgente->where('estado','=','Espera')->count();
                            $rechazados = $solicitudesAgente->where('estado','=','Rechazada')->count();
                            $totalAgente = $agendados + $espera + $rechazados;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="text-center border-grey-light border p-3">{{ $agente->id }}</td>
                            <td class="text-center border-grey-light border p-3 font-medium">{{ $agente->name }} {{ $agente->apellido1 }} {{ $agente->apellido2 }}</td>
                            <td class="text-center border-grey-light border p-3"><span class="badge-agendado">{{ $agendados }}</span></td>
                            <td class="text-center border-grey-light border p-3"><span class="badge-espera">{{ $espera }}</span></td>
                            <td class="text-center border-grey-light border p-3"><span class="badge-rechazada">{{ $rechazados }}</span></td>
                            <td class="text-center border-grey-light border p-3 font-bold">{{ $totalAgente }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="5" class="text-right p-3">Total General:</td>
                            <td class="text-center border-grey-light border p-3">{{ count($solicitudes) }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Detalle de solicitudes por agente --}}
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalle de Solicitudes por Colaborador</h3>
                @foreach($agentes as $agente)
                @php
                    $solicitudesDetalleAgente = $solicitudes->where('usercod','=',$agente->id);
                @endphp
                @if($solicitudesDetalleAgente->count() > 0)
                <div class="mb-6 border rounded-lg overflow-hidden">
                    <div class="bg-blue-100 p-3 flex justify-between items-center cursor-pointer" onclick="toggleAgente('agente-{{ $agente->id }}')">
                        <h4 class="font-semibold text-blue-800">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $agente->name }} {{ $agente->apellido1 }} {{ $agente->apellido2 }}
                        </h4>
                        <span class="text-sm text-blue-600">{{ $solicitudesDetalleAgente->count() }} solicitudes</span>
                    </div>
                    <div id="agente-{{ $agente->id }}" class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="p-2 text-left">ID</th>
                                    <th class="p-2 text-left">Paciente</th>
                                    <th class="p-2 text-left">Documento</th>
                                    <th class="p-2 text-left">EPS</th>
                                    <th class="p-2 text-left">Servicio</th>
                                    <th class="p-2 text-center">Estado</th>
                                    <th class="p-2 text-left">Motivo</th>
                                    <th class="p-2 text-left">Fecha Actualización</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudesDetalleAgente as $solicitud)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ $solicitud->id }}</td>
                                    <td class="p-2">{{ $solicitud->paciente_nombre }} {{ $solicitud->paciente_apellido1 }} {{ $solicitud->paciente_apellido2 }}</td>
                                    <td class="p-2">{{ $solicitud->tipo_doc ?? '' }} {{ $solicitud->paciente_ndocumento }}</td>
                                    <td class="p-2">{{ $solicitud->eps_nombre ?? 'N/A' }}</td>
                                    <td class="p-2">{{ $solicitud->servicio_nombre }}</td>
                                    <td class="p-2 text-center">
                                        @if($solicitud->estado == 'Agendado')
                                            <span class="badge-agendado">{{ $solicitud->estado }}</span>
                                        @elseif($solicitud->estado == 'Espera')
                                            <span class="badge-espera">{{ $solicitud->estado }}</span>
                                        @elseif($solicitud->estado == 'Rechazada')
                                            <span class="badge-rechazada">{{ $solicitud->estado }}</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-200 rounded-full text-xs">{{ $solicitud->estado }}</span>
                                        @endif
                                    </td>
                                    <td class="p-2 text-xs">
                                        @if($solicitud->estado == 'Rechazada' && $solicitud->motivo_rechazo)
                                            {{ $solicitud->motivo_rechazo }}
                                        @elseif($solicitud->estado == 'Espera' && $solicitud->motivo_espera)
                                            {{ $solicitud->motivo_espera }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-2">{{ \Carbon\Carbon::parse($solicitud->updated_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                @endforeach

                {{-- Botón exportar hoy --}}
                <div class="flex justify-end mt-4">
                    @if(!$filtro_ok)
                    <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-1" wire:click="exportarHoy()">Exportar hoy</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleAgente(id) {
            var element = document.getElementById(id);
            if (element.style.display === 'none') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }
    </script>
</div>