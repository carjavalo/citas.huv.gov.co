<x-app-layout>
<x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-600 leading-tight">
           Reporte de Todas las Solicitudes 
        </h2>
    </x-slot>        

<div class="py-12">
   <div class="max-w-8xl mx-auto sm:px-6 lg:px-12">
           @livewire('filter-reporte-citas');
          
   </div> 
</div>    
</x-app-layout>






