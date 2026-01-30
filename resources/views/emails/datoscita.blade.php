@component('mail::message')
Cordial saludo

Se envía adjunto cita asignada para el paciente {{ $paciente }}, por favor presentarse 40 minutos antes de la cita, traer autorización vigente, orden médica, historia clínica y copia del documento de identidad del paciente con resultados de exámenes. <br>
Para cancelación de citas favor comunicarse al <b>6206000 ext 5555</b>

Fecha de la cita: {{ $fecha }} <br>
Hora de la cita: {{ $hora }} <br>
Lugar para su atención: {{ $ubicacion }} <br>
Código de cita: {{ $reserva }}  <br>

@if($mensaje)
{{ $mensaje }} <br>
@endif

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios Ambulatorios - Unidad Básica de Atención
@endcomponent
