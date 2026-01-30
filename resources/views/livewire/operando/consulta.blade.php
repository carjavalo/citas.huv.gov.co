<div class="h-screen flex flex-col bg-gray-50">
    <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Encabezado -->
            <div class="flex-shrink-0 p-4 shadow-md" style="background-color: #2c4370;">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        <div>
                            <h2 class="text-2xl font-bold text-white">SQL Console - Base de Datos Citas</h2>
                            <p class="text-gray-200 text-sm">Ejecutar consultas SQL directamente en MySQL</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded-full">SUPER ADMIN</span>
                </div>
            </div>

            <div class="flex-1 flex overflow-hidden mt-6">
                <!-- Panel izquierdo - Lista de tablas -->
                <div class="w-64 flex-shrink-0 flex flex-col overflow-hidden">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                        <div class="p-3 font-semibold text-white flex-shrink-0" style="background-color: #2c4370;">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Tablas ({{ count($tables) }})
                        </div>
                        <div class="flex-1 overflow-y-auto">
                            @foreach($tables as $table)
                                @php
                                    $tableName = array_values((array) $table)[0];
                                @endphp
                                <button wire:click="selectTable('{{ $tableName }}')" 
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 border-b border-gray-100 flex items-center transition-colors {{ $selectedTable === $tableName ? 'bg-blue-100 font-semibold' : '' }}"
                                    style="color: #2c4370;">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $tableName }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Estructura de tabla seleccionada -->
                        @if(!empty($tableStructure))
                        <div class="border-t border-gray-200">
                            <div class="p-3 font-semibold text-white text-sm" style="background-color: #4a5568;">
                                Estructura: {{ $selectedTable }}
                            </div>
                            <div class="max-h-48 overflow-y-auto">
                                <table class="w-full text-xs">
                                    <thead class="bg-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left">Campo</th>
                                            <th class="px-2 py-1 text-left">Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tableStructure as $column)
                                            @php
                                                $col = (object) $column;
                                            @endphp
                                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                                <td class="px-2 py-1 font-mono {{ ($col->Key ?? '') === 'PRI' ? 'font-bold text-blue-600' : '' }}">
                                                    {{ $col->Field ?? '' }}
                                                    @if(($col->Key ?? '') === 'PRI')
                                                        <span class="text-yellow-600">🔑</span>
                                                    @endif
                                                </td>
                                                <td class="px-2 py-1 text-gray-600">{{ $col->Type ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Panel derecho - Editor SQL y resultados -->
                <div class="flex-1 flex flex-col overflow-hidden px-4">
                    <!-- Editor SQL -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden flex-shrink-0">
                        <div class="p-3 font-semibold text-white flex items-center justify-between" style="background-color: #2c4370;">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                Ejecutar consulta SQL
                            </div>
                            <div class="text-xs text-gray-300">
                                Ctrl+Enter para ejecutar
                            </div>
                        </div>
                        <div class="p-3">
                            <textarea 
                                wire:model.defer="query"
                                wire:keydown.ctrl.enter="executeQuery"
                                class="w-full h-24 p-3 font-mono text-sm border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none resize-none"
                                style="background-color: #1e1e1e; color: #d4d4d4;"
                                placeholder="SELECT * FROM usuarios WHERE id = 1;&#10;&#10;-- Escriba su consulta SQL aquí...&#10;-- Puede usar SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, DROP, etc."
                                spellcheck="false"
                            ></textarea>
                            
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex gap-2">
                                    <button wire:click="executeQuery" 
                                        class="px-3 py-1.5 text-white text-sm font-semibold rounded-lg shadow hover:opacity-90 transition-opacity flex items-center"
                                        style="background-color: #059669;">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ejecutar
                                    </button>
                                    <button wire:click="clearQuery" 
                                        class="px-3 py-1.5 bg-gray-500 text-white text-sm font-semibold rounded-lg shadow hover:bg-gray-600 transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Limpiar
                                    </button>
                                    <button wire:click="exportToExcel" 
                                        class="px-3 py-1.5 bg-green-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-green-700 transition-colors flex items-center"
                                        @if(empty($results)) disabled @endif>
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Excel
                                    </button>
                                    <button wire:click="backupDatabase" 
                                        class="px-3 py-1.5 bg-blue-600 text-white text-sm font-semibold rounded-lg shadow hover:bg-blue-700 transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                        </svg>
                                        Backup
                                    </button>
                                </div>
                                
                                <!-- Consultas rápidas -->
                                <div class="flex gap-1">
                                    <button wire:click="$set('query', 'SHOW TABLES;')" 
                                        class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300">
                                        SHOW TABLES
                                    </button>
                                    <button wire:click="$set('query', 'SHOW DATABASES;')" 
                                        class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded hover:bg-gray-300">
                                        SHOW DATABASES
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mensaje de resultado -->
                    @if($message)
                    <div class="mb-3 p-2 rounded-lg flex items-center text-sm {{ $messageType === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : ($messageType === 'error' ? 'bg-red-100 text-red-800 border border-red-300' : 'bg-yellow-100 text-yellow-800 border border-yellow-300') }}">
                        @if($messageType === 'success')
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($messageType === 'error')
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                        <span class="flex-1">{{ $message }}</span>
                        @if($executionTime > 0)
                            <span class="text-xs ml-2 opacity-75">({{ $executionTime }} ms)</span>
                        @endif
                    </div>
                    @endif

                    <!-- Resultados -->
                    <div class="flex-1 bg-white rounded-lg shadow-md overflow-hidden flex flex-col">
                        @if(!empty($results))
                            <div class="p-2 font-semibold text-white flex items-center justify-between flex-shrink-0 text-sm" style="background-color: #4a5568;">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Resultados ({{ count($results) }} filas)
                                </div>
                                <span class="text-xs text-gray-300">{{ count($columns) }} columnas</span>
                            </div>
                            <div class="flex-1 overflow-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-100 sticky top-0">
                                        <tr>
                                            <th class="px-2 py-1 text-left text-xs font-medium text-gray-600 uppercase tracking-wider bg-gray-200">#</th>
                                            @foreach($columns as $column)
                                                <th class="px-2 py-1 text-left text-xs font-medium text-gray-600 uppercase tracking-wider bg-gray-100 whitespace-nowrap">
                                                    {{ $column }}
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($results as $index => $row)
                                            @php $row = (object) $row; @endphp
                                            <tr class="hover:bg-blue-50 transition-colors {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                                <td class="px-2 py-1 whitespace-nowrap text-xs text-gray-400 bg-gray-100">{{ $index + 1 }}</td>
                                                @foreach($columns as $column)
                                                    @php $value = $row->$column ?? null; @endphp
                                                    <td class="px-2 py-1 text-sm text-gray-900 font-mono max-w-xs truncate" title="{{ $value }}">
                                                        @if(is_null($value))
                                                            <span class="text-gray-400 italic">NULL</span>
                                                        @elseif($value === '')
                                                            <span class="text-gray-400 italic">(vacío)</span>
                                                        @else
                                                            {{ Str::limit($value, 50) }}
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="flex-1 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                    </svg>
                                    <h3 class="text-base font-medium text-gray-600 mb-1">Ejecute una consulta SQL</h3>
                                    <p class="text-sm text-gray-500">Seleccione una tabla del panel izquierdo o escriba su consulta SQL</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Advertencia de seguridad -->
            <div class="flex-shrink-0 p-2 bg-yellow-50 border-t border-yellow-200 flex items-start">
                <svg class="w-4 h-4 text-yellow-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div class="text-xs text-yellow-800">
                    <strong>Advertencia:</strong> Esta herramienta ejecuta comandos SQL directamente en la base de datos. Las operaciones son irreversibles. Use con precaución.
                </div>
            </div>
        </div>
    </div>
</div>
