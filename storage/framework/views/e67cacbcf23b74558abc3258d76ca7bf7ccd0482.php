<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AppLayout::class, []); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
 <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-blue-600 leading-tight">
           Citas Pendientes y en Espera 
        </h2>
     <?php $__env->endSlot(); ?>        

<div class="py-12">
   <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
           <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('filter-reporte-citas')->html();
} elseif ($_instance->childHasBeenRendered('6wwB5hX')) {
    $componentId = $_instance->getRenderedChildComponentId('6wwB5hX');
    $componentTag = $_instance->getRenderedChildComponentTagName('6wwB5hX');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('6wwB5hX');
} else {
    $response = \Livewire\Livewire::mount('filter-reporte-citas');
    $html = $response->html();
    $_instance->logRenderedChild('6wwB5hX', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
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