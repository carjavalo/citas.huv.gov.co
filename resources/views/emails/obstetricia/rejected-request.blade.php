@component('mail::message')
Cordial saludo

La solicitud para la paciente {{$paciente->primer_nombre}} {{$paciente->segundo_nombre}} {{$paciente->primer_apellido}} {{$paciente->segundo_apellido}} fue rechazada.<br>

@if($observacion)
{{ $observacion }} <br>
@endif

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios de Atención Telesalud
@endcomponent
