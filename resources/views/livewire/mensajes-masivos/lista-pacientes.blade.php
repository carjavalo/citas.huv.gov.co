{{-- Lista de pacientes con los datos de su cita (confirmación y resultado del envío). --}}
<div class="mt-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg bg-white">
    <table class="min-w-full divide-y divide-gray-100 text-xs text-gray-700">
        <thead class="bg-gray-50 sticky top-0">
            <tr class="text-left font-semibold text-gray-600 uppercase tracking-wider">
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Nombre del paciente</th>
                <th class="px-3 py-2">Número de documento</th>
                <th class="px-3 py-2">Especialidad</th>
                <th class="px-3 py-2">Fecha y hora</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach ($pacientes as $paciente)
                <tr>
                    <td class="px-3 py-2 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="px-3 py-2 font-medium text-gray-800">{{ $paciente['nombre'] }}</td>
                    <td class="px-3 py-2 font-mono">{{ $paciente['documento'] }}</td>
                    <td class="px-3 py-2">{{ $paciente['especialidad'] }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $paciente['fecha'] }} {{ $paciente['hora'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
