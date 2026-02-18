
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Configuración de Mantenimiento por Roles ("P_Downd")
                </h2>
                <span class="px-3 py-1 text-sm font-semibold {{ $is_maintenance_on ? 'text-red-800 bg-red-100 rounded-full' : 'text-green-800 bg-green-100 rounded-full' }}">
                    Estado: {{ $is_maintenance_on ? 'Activo (Restringido)' : 'Inactivo (Normal)' }}
                </span>
            </div>

            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800 font-medium">
                            Esta opción restringe el acceso temporalmente a usuarios con los roles seleccionados. 
                        </p>
                        <p class="text-sm text-yellow-700 mt-1">
                            El rol <strong>Super Admin</strong> nunca será bloqueado.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Columna Izquierda: Control Principal -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Control de Acceso</h3>
                    
                    <div class="flex items-center justify-between mb-6">
                        <span class="flex-grow flex flex-col">
                            <span class="text-base font-medium text-gray-900">Activar Restricción Global</span>
                            <span class="text-sm text-gray-500">Habilita el modo mantenimiento para los roles marcados.</span>
                        </span>
                        
                        <!-- Toggle Switch: pasa el NUEVO estado como parámetro explícito -->
                        <button type="button" wire:click="toggleMaintenance({{ $is_maintenance_on ? 'false' : 'true' }})" 
                                class="{{ $is_maintenance_on ? 'bg-red-600' : 'bg-gray-300' }} relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" 
                                role="switch" aria-checked="{{ $is_maintenance_on ? 'true' : 'false' }}">
                            <span aria-hidden="true" class="{{ $is_maintenance_on ? 'translate-x-5' : 'translate-x-0' }} pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                        <span class="ml-3 text-xs font-semibold {{ $is_maintenance_on ? 'text-red-600' : 'text-green-600' }}">
                            {{ $is_maintenance_on ? '● ACTIVO' : '○ INACTIVO' }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Mensaje para el usuario bloqueado</label>
                        <textarea wire:model="message" id="message" rows="4" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                  placeholder="El sistema se encuentra en mantenimiento..."></textarea>
                        <p class="mt-2 text-xs text-gray-500">Este mensaje aparecerá en la pantalla de error 503.</p>
                    </div>
                </div>

                <!-- Columna Derecha: Selección de Roles -->
                <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="text-lg font-medium text-gray-900 mb-2 border-b pb-2">Roles Afectados</h3>
                    <p class="text-xs text-gray-500 mb-3">Marque los roles que <strong>NO</strong> podrán acceder.</p>
                    
                    <div class="max-h-80 overflow-y-auto pr-2 space-y-2">
                        @foreach($roles as $role)
                            <label class="flex items-center p-2 rounded hover:bg-gray-50 cursor-pointer border border-transparent hover:border-gray-200">
                                <input wire:model="selected_roles" 
                                       value="{{ $role }}" 
                                       type="checkbox" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition duration-150 ease-in-out">
                                <span class="ml-3 block text-sm font-medium text-gray-700">
                                    {{ $role }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end border-t pt-4">
                <x-jet-action-message class="mr-3" on="saved">
                    {{ __('Configuración guardada.') }}
                </x-jet-action-message>

                <x-jet-button wire:click="save" wire:loading.attr="disabled" class="bg-blue-800 hover:bg-blue-700">
                    {{ __('Guardar Cambios') }}
                </x-jet-button>
            </div>
        </div>
    </div>

    <!-- Script para alertas SweetAlert2 -->
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('alertSuccess', message => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Éxito',
                        text: message,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    alert('Éxito: ' + message);
                }
            });
        });
    </script>
</div>

