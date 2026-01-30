<div>
    <?php if($modalCrear): ?>
        <div>
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('sedes.crear')->html();
} elseif ($_instance->childHasBeenRendered('l3835126715-0')) {
    $componentId = $_instance->getRenderedChildComponentId('l3835126715-0');
    $componentTag = $_instance->getRenderedChildComponentTagName('l3835126715-0');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l3835126715-0');
} else {
    $response = \Livewire\Livewire::mount('sedes.crear');
    $html = $response->html();
    $_instance->logRenderedChild('l3835126715-0', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?> 
        </div>
    <?php endif; ?>
    <?php if($modal): ?>
        <div>
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('sedes.crear', ['sede_id' => $sede_id])->html();
} elseif ($_instance->childHasBeenRendered('l3835126715-1')) {
    $componentId = $_instance->getRenderedChildComponentId('l3835126715-1');
    $componentTag = $_instance->getRenderedChildComponentTagName('l3835126715-1');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l3835126715-1');
} else {
    $response = \Livewire\Livewire::mount('sedes.crear', ['sede_id' => $sede_id]);
    $html = $response->html();
    $_instance->logRenderedChild('l3835126715-1', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?> 
        </div>
    <?php endif; ?>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-200 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="p-4 flex justify-between items-center">
                                <h2 class="text-xl font-semibold text-gray-800">Gestión de Sedes</h2>
                                <button wire:click="abrirModalCrear" class="transition ease-in-out delay-150 bg-green-500 hover:-translate-y-1 hover:scale-110 hover:bg-green-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Crear Sede
                                </button>
                            </div>
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Nombre
                                                <input type="text" wire:model.debounce.300ms="busqueda" placeholder="Buscar sede o ciudad..." class="mt-1 block w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ciudad
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Estado
                                                <select wire:model="filtroEstado" class="mt-1 block w-full focus:ring-blue-500 focus:border-blue-500 shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                    <option value="">Todos</option>
                                                    <option value="1">Activo</option>
                                                    <option value="0">Inactivo</option>
                                                </select>
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__empty_1 = true; $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">                                      
                                                    <?php echo e($sede->nombre); ?>                                                  
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">                                      
                                                    <?php echo e($sede->ciudad); ?>                                                  
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <?php if($sede->estado == true): ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"> Activo </span>
                                                    <?php else: ?>
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"> Inactivo </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <button wire:click="abrirModalEditar(<?php echo e($sede->id); ?>)" class="transition ease-in-out delay-150 bg-yellow-500 hover:-translate-y-1 hover:scale-110 hover:bg-yellow-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                                        Editar
                                                    </button>
                                                    <button onclick="confirmarEliminacion(<?php echo e($sede->id); ?>)" class="transition ease-in-out delay-150 bg-red-500 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white ml-2">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                                    No se encontraron sedes
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <div class="px-4 py-3">
                                    <?php echo e($sedes->links()); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('eliminarSede', id);
                }
            })
        }
    </script>
</div>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/livewire/sedes/consulta.blade.php ENDPATH**/ ?>