<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-200 overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- This example requires Tailwind CSS v2.0+ -->
                <div class="flex flex-col">
                    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table  class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Usuarios
                                                <input type="text" wire:model="nombre" id="" placeholder="Filtrar por Nombres" class="mt-1 inline focus:ring-blue-500 focus:blue-indigo-500 w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <input type="text" wire:model="apellidos1" id="" placeholder="Filtrar por Primer Apellido" class="mt-1 inline focus:ring-blue-500 focus:blue-indigo-500 w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <input type="text" wire:model="identificacion" id="" placeholder="Filtrar por Documento" class="mt-1 inline focus:ring-blue-500 focus:blue-indigo-500 w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </th>                                        
                                           <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Usuarios
                                                <input type="text" wire:model="ndocumentos" id="" placeholder="Filtrar por apellido" class="mt-1 inline focus:ring-blue-500 focus:blue-indigo-500 w-auto shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            </th>-->
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Rol
                                            </th>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.usuarios.update')): ?>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Acciones
                                            </th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $usuarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $usuario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="ml-1">
                                                            <div class="text-sm font-medium text-gray-900">
                                                            <?php echo e($usuario->name); ?> <?php echo e($usuario->apellido1); ?> <?php echo e($usuario->apellido2); ?>

                                                            </div>
                                                            <div class="text-sm text-gray-500">
                                                                <?php echo e($usuario->email); ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <?php if($usuario->roles->first()): ?>
                                                    <span class=""><?php echo e($usuario->roles->first()->name); ?></span>
                                                <?php else: ?>
                                                    <span class="bg-zinc-200 rounded-lg">Sin rol</span>
                                                <?php endif; ?>
                                                </td>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.usuarios.update')): ?>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <button wire:click="editar(<?php echo e($usuario->id); ?>)" class="justify-center transition ease-in-out delay-150 bg-yellow-500 hover:-translate-y-1 hover:scale-110 hover:bg-yellow-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                                            Editar
                                                        </button>
                                                        
                                                        <?php if(auth()->user()->hasRole('Super Admin')): ?>
                                                            <button 
                                                                wire:click="eliminar(<?php echo e($usuario->id); ?>)" 
                                                                onclick="return confirm('¿Está seguro de eliminar este usuario? Esta acción no se puede deshacer.')"
                                                                class="justify-center transition ease-in-out delay-150 bg-red-500 hover:-translate-y-1 hover:scale-110 hover:bg-red-700 duration-300 inline-flex py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white">
                                                                Eliminar
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <?php echo e($usuarios->links()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if($modal): ?>
        <?php echo $__env->make('livewire.usuarios.editar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/livewire/usuarios/consulta.blade.php ENDPATH**/ ?>