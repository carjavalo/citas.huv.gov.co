<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AppLayout::class, []); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
 <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-blue-600 leading-tight">
           Reporte de Todas las Solicitudes 
        </h2>
     <?php $__env->endSlot(); ?>        

<div class="py-12">
   <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
           <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('filter-reporte-citas')->html();
} elseif ($_instance->childHasBeenRendered('mPKjr9q')) {
    $componentId = $_instance->getRenderedChildComponentId('mPKjr9q');
    $componentTag = $_instance->getRenderedChildComponentTagName('mPKjr9q');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('mPKjr9q');
} else {
    $response = \Livewire\Livewire::mount('filter-reporte-citas');
    $html = $response->html();
    $_instance->logRenderedChild('mPKjr9q', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>;
          
   </div> 
</div>    
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>






<?php /**PATH C:\xampp\htdocs\citas\resources\views/livewire/reporte/citas/reportecitas.blade.php ENDPATH**/ ?>