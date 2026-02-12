<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
    <div class="flex justify-center min-h-max pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 trasition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-x1 transform trasition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="bg-blue-100 rounded-lg mb-4 text-center">
                    <label class="text-2xl" for="">Información de la cita</label>
                </div>
                <div class="bg-gray-100 rounded-lg grid grid-cols-6 gap-6 mb-2 select-text">
                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label for="">Paciente:</label>
                        <p><?php echo e($usu_nomb); ?></p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label>Contacto:</label>
                        <p><?php echo e($contacto); ?></p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2 ">
                        <label>Tipo de documento:</label>
                        <p><?php echo e($tipo_documento); ?></p>
                    </div>

                    <div class="col-span-6 sm:col-span-3 mx-2">
                        <label>Numero de documento:</label>
                        <p><?php echo e($ndocumento); ?></p>
                    </div>

                    <div class="col-span-6 mx-2 mb-2">
                        <label for="">Documentos:</label>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            <a target="_blank" href="<?php echo e(\App\Http\Controllers\DocumentoController::generarUrl($archivos['historia'])); ?>">Historia</a>
                        </span>
                        <?php if($archivos['autorizacion']): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <a target="_blank" href="<?php echo e(\App\Http\Controllers\DocumentoController::generarUrl($archivos['autorizacion'])); ?>">Autorizacion</a>
                            </span>
                        <?php endif; ?>
                        <?php if($archivos['soporte_patologia']): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <a target="_blank" href="<?php echo e(\App\Http\Controllers\DocumentoController::generarUrl($archivos['soporte_patologia'])); ?>">Soporte patología</a>
                            </span>
                        <?php endif; ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <a target="_blank" href="<?php echo e(\App\Http\Controllers\DocumentoController::generarUrl($archivos['orden'])); ?>">Orden Médica</a>
                            </span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <a target="_blank" href="<?php echo e(\App\Http\Controllers\DocumentoController::generarUrl($archivos['documento'])); ?>">Documento</a>
                            </span>
                    </div>
                    <?php if($observacion): ?>
                    <div class="col-span-6 sm:col-span-6 mx-1 sm:row-span-1 bg-white mb-2 rounded-lg">
                        <label>Observación</label>
                        <p class="mx-2 text-justify"><?php echo e($observacion); ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if($codigo_autorizacion): ?>
                    <div class="col-span-6 sm:col-span-6 mx-1 sm:row-span-1 bg-white mb-2 rounded-lg">
                        <label>Código de autorización</label>
                        <p class="mx-2 text-justify"><?php echo e($codigo_autorizacion); ?></p>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha de la cita</label>
                        <input wire:model="fecha" type="date" id="fecha" min="<?php echo e($hoy); ?>" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <?php $__errorArgs = ['fecha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="hora" class="block text-sm font-medium text-gray-700">Hora de la cita</label>
                        <input wire:model="hora" type="time" id="hora" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <?php $__errorArgs = ['hora'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="reserva" class="block text-sm font-medium text-gray-700">Número reserva de la cita</label>
                        <input wire:model="reserva" type="text" id="reserva" autocomplete="off" class="mt-1 focus:ring-blue-500 focus:blue-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <?php $__errorArgs = ['reserva'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="medico" class="block text-sm font-medium text-gray-700">Certificado</label>
                        <input wire:model="adjunto.0" type="file" id="certificado" autocomplete="off" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                        <?php $__errorArgs = ['adjunto.0'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="medico" class="block text-sm font-medium text-gray-700">Adjunto 1</label>
                        <input wire:model="adjunto.1" type="file" id="adjunto1" autocomplete="off" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                        <?php $__errorArgs = ['adjunto.1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="medico" class="block text-sm font-medium text-gray-700">Adjunto 2</label>
                        <input wire:model="adjunto.2" type="file" id="adjunto2" autocomplete="off" class="form-control block w-full px-3 py-1.5 text-base font-normal text-gray-70 rounded transition ease-in-out m-0 file:border-0 file:bg-blue-50 file:text-blue-700  file:rounded-full">
                        <?php $__errorArgs = ['adjunto.2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3 space-x-4 items-center">
                        <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-2">Ubicación</label>
                        <input wire:model.defer="ubicacion" type="hidden" value="Calle. 5 # 36 - 08, Barrio San Fernando Cali, Valle del Cauca">Sur-Cl. 5 # 36 - 08, Barrio San Fernando Cali, Valle del Cauca
                        <!-- <input wire:model="ubicacion" type="radio" value="Sede Norte Clínica Valle Solidario Avenida 3N # 32AN-40, Cali, Valle del Cauca">Norte -->
                        <?php $__errorArgs = ['ubicacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo a notificar</label>
                        <input wire:model="correo" type="text" id="email" autocomplete="off" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" disabled>
                    </div>
                    
                    <div class="col-span-6 sm:col-span-6">

                        <label for="obs" class="block w-full text-sm font-medium text-gray-700">Mensaje</label>
                        <textarea wire:model="mensaje" id="obs" class="rounded-lg border border-solid w-full h-auto border-gray-300"></textarea>
                        <?php $__errorArgs = ['observacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500 text-white"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    </div>

                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                        <button wire:click.prevent="cita()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Notificar
                        </button>
                    </span>

                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button wire:click="cancelar()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Cancelar
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\citas\resources\views/livewire/citas/cita-agendada.blade.php ENDPATH**/ ?>