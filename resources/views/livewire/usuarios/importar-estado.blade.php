{{-- Pastilla con el resultado de una fila del archivo. Espera $estado. --}}
@php
    $estilos = [
        \App\Models\ImportacionUsuarioDetalle::NUEVO     => ['impu-azul', 'person_add'],
        \App\Models\ImportacionUsuarioDetalle::IMPORTADO => ['impu-verde', 'check_circle'],
        \App\Models\ImportacionUsuarioDetalle::EXISTENTE => ['impu-ambar', 'person_search'],
        \App\Models\ImportacionUsuarioDetalle::OMITIDO   => ['impu-rojo', 'block'],
    ];
    [$color, $icono] = $estilos[$estado] ?? ['impu-gris', 'help_outline'];
@endphp
<span class="impu-pastilla {{ $color }}">
    <span class="material-icons">{{ $icono }}</span>
    {{ \App\Models\ImportacionUsuarioDetalle::ESTADOS[$estado] ?? $estado }}
</span>
