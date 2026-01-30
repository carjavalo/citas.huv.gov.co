<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
    <div style="background-image: url('<?php echo e(asset('img/huv_fondo_2.jpg')); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;" class="flex items-center justify-center">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="https://citas.huv.gov.co/huv-icon.png" alt="Logo HUV" class="w-20 h-20">
            </div>

            <div class="mb-4 text-sm text-gray-600">
                <?php echo e(__('¡Gracias por registrarte! Antes de que comiences, debes verificar tu cuenta haciendo click en el enlace que te hemos enviado. Si no recibiste el correo, te enviaremos otro.')); ?>

            </div>

            <?php if(session('status') == 'verification-link-sent'): ?>
                <div class="mb-4 font-medium text-sm text-green-600">
                    <?php echo e(__('Un nuevo enlace de verificación ha sido enviado a la dirección de correo electrónico que registraste.')); ?>

                </div>
            <?php endif; ?>

            <div class="mt-4 flex items-center justify-between">
                <form method="POST" action="<?php echo e(route('verification.send')); ?>">
                    <?php echo csrf_field(); ?>

                    <div>
                        <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.button','data' => ['type' => 'submit']]); ?>
<?php $component->withName('jet-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['type' => 'submit']); ?>
                            <?php echo e(__('Reenviar correo de verificación')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                    </div>
                </form>

                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>

                    <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                        <?php echo e(__('Cerrar sesión')); ?>

                    </button>
                </form>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>