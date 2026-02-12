<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e('Citas HUV', config('app.name')); ?></title>
        <link rel="icon" href="<?php echo e(asset('img/favicon2.png')); ?>" type="image/png">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="<?php echo e(mix('css/app.css')); ?>">

        <!-- Scripts -->
        <script src="<?php echo e(mix('js/app.js')); ?>" defer></script>
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            <?php echo e($slot); ?>

        </div>

        <?php echo \Livewire\Livewire::scripts(); ?>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Livewire.hook('message.failed', (message, component) => {
                if (message.status === 419) {
                    location.reload();
                }
            });
        </script>
    </body>
</html>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/layouts/guest.blade.php ENDPATH**/ ?>