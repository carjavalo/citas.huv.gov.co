    
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
                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.input','data' => ['wire:model' => 'filters.fromDate','name' => 'fecha1','type' => 'date','class' => 'w-36']]); ?>
<?php $component->withName('jet-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['wire:model' => 'filters.fromDate','name' => 'fecha1','type' => 'date','class' => 'w-36']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                    
                </div>
                <div> 
                    Filtrar hasta:
                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.input','data' => ['wire:model' => 'filters.toDate','type' => 'date','class' => 'w-36']]); ?>
<?php $component->withName('jet-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['wire:model' => 'filters.toDate','type' => 'date','class' => 'w-36']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                </div>

                <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', 'Super Admin')): ?>
                <div>
                    Sede:
                    <select wire:model="filSede" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todas las sedes</option>
                        <?php $__currentLoopData = $sedes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sede): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sede->id); ?>"><?php echo e($sede->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    Servicio:
                    <select wire:model="filServicio" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todos los servicios</option>
                        <?php $__currentLoopData = $pservicios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pservicio): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($pservicio->id); ?>"><?php echo e($pservicio->descripcion); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    Especialidad:
                    <select wire:model="filEspecialidad" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                        <option value="">Todas las especialidades</option>
                        <?php $__currentLoopData = $especialidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $especialidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($especialidad->servcod); ?>"><?php echo e($especialidad->servnomb); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
              <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.button','data' => ['wire:click' => 'generateReport']]); ?>
<?php $component->withName('jet-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['wire:click' => 'generateReport']); ?>
                Generar Reporte
               <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
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
                    <?php $__currentLoopData = $solicitudes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $solicitude): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                      <?php echo e($solicitude->espec); ?>

                            </th>
                            <td class="px-6 py-4">
                                   <?php echo e($solicitude->servicio_nombre); ?>

                            </td>
                            <td class="px-6 py-4">
                                     <?php echo e($solicitude->estado); ?>

                            </td>
                            <td class="px-6 py-4">
                                      <?php echo e($solicitude->created_at->format('d/m/y')); ?>

                            </td>
                            <td class="px-6 py-4">
                                <?php echo e($solicitude->updated_at->format('d/m/y')); ?>

                            </td>
                            <td class="px-6 py-4">
                                      <?php echo e($solicitude->paciente_nombre); ?>

                            </td>
                            <td class="px-6 py-4">
                                       <?php echo e($solicitude->paciente_apellido1); ?>

                            </td>
                            <td class="px-6 py-4">
                                       <?php echo e($solicitude->paciente_apellido2); ?>

                            </td>
                            <td class="px-6 py-4">
                                       <?php echo e($solicitude->paciente_ndocumento); ?>

                            </td>
                            <td class="px-6 py-4">
                                        <?php echo e($solicitude->codigo_eps); ?>

                            </td>
                            <td class="px-6 py-4">
                                    <?php echo e($solicitude->eps); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
                <div class="mt-4">
                    <?php echo e($solicitudes->links()); ?>

                </div>
    </div>  
   <?php /**PATH C:\xampp\htdocs\citas\resources\views/livewire/filter-reporte-citas.blade.php ENDPATH**/ ?>