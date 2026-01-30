@component('mail::message')
Cordial saludo,

Estimado(a) {{ $usuario }}, le informamos que su cita para la especialidad de **{{ $especialidad }}** ha sido cancelada.

Si tiene alguna duda o requiere reagendar su cita, por favor comuníquese con nosotros.

Cualquier inquietud por favor comunicarse al 6206275. <br>

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b> <br>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios Ambulatorios - Unidad Básica de Atención
@endcomponent
