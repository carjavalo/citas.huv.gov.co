<div>
    <div class="bg-white h-full overflow-hidden shadow-xl sm:rounded-lg">

        <body class="flex items-center justify-center">
            <div class="flex justify-center">
                <p class="text-center sm:text-sm md:text-base lg:text-lg xl:text-xl 2xl:text-3xl my-4">ESTADO</p>
            </div>
            <div wire:poll.visible>
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="text-white">
                        <tr class="bg-blue-500 flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                            <th class="p-3 text-center sm:text-sm md:text-base lg:text-lg xl:text-xl 2xl:text-2xl">Quirófano</th>
                            <th class="p-3 text-center sm:text-sm md:text-base lg:text-lg xl:text-xl 2xl:text-2xl">Paciente</th>
                            <th class="p-3 text-center sm:text-sm md:text-base lg:text-lg xl:text-xl 2xl:text-2xl">Estado</th>
                            <th class="p-3 text-center sm:text-sm md:text-base lg:text-lg xl:text-xl 2xl:text-2xl">Última actualización</th>
                        </tr>
                    </thead>
                    <tbody class="flex-1 sm:flex-none">
                        @foreach($pacientes as $paciente)
                        <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3">{{ $paciente->id }}</td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3">
                                <p class="capitalize">{{ $paciente->name }} {{ $paciente->apellido1 }} {{ $paciente->apellido2 }}</p>
                            </td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3 truncate"></td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3 truncate"></td>
                        </tr>
                        @endforeach
                        <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3">12</td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3">
                                <p class="capitalize">ave maria</p>
                            </td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3 truncate"></td>
                            <td class="text-center text-3xl border-grey-light border hover:bg-gray-100 p-3 truncate"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </body>
    </div>

</div>