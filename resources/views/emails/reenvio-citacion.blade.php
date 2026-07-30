@component('mail::message')
Cordial saludo

Se reenvía el certificado de la cita asignada para el paciente {{ $paciente }}@if($servicio), en el servicio de {{ $servicio }}@endif. Los datos de fecha, hora y lugar de atención se encuentran en el documento adjunto.

Por favor presentarse 40 minutos antes de la cita, traer autorización vigente, orden médica, historia clínica y copia del documento de identidad del paciente con resultados de exámenes. <br>
Para cancelación de citas favor comunicarse al <b>6206000 ext 5555</b>

@if($mensaje)
{{ $mensaje }} <br>
@endif

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios Ambulatorios - Unidad Básica de Atención
@endcomponent
