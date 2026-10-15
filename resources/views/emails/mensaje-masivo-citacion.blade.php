@component('mail::message')
{{-- Bloque HTML sin líneas en blanco: Markdown no interpreta su contenido, así el texto del Excel no puede convertirse en enlaces ni formato. --}}
<p>{!! nl2br(e($mensaje)) !!}</p>

@if($urlConfirmacion)
Por favor confírmenos si asistirá a la cita:

@component('mail::button', ['url' => $urlConfirmacion, 'color' => 'primary'])
Ir a confirmar
@endcomponent
@endif

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios Ambulatorios - Unidad Básica de Atención
@endcomponent
