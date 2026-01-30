<?php $__env->startComponent('mail::message'); ?>
Cordial saludo

Se envía adjunto cita asignada para el paciente <?php echo e($paciente); ?>, por favor presentarse 40 minutos antes de la cita, traer autorización vigente, orden médica, historia clínica y copia del documento de identidad del paciente con resultados de exámenes. <br>
Para cancelación de citas favor comunicarse al <b>6206000 ext 5555</b>

Fecha de la cita: <?php echo e($fecha); ?> <br>
Hora de la cita: <?php echo e($hora); ?> <br>
Lugar para su atención: <?php echo e($ubicacion); ?> <br>
Código de cita: <?php echo e($reserva); ?>  <br>

<?php if($mensaje): ?>
<?php echo e($mensaje); ?> <br>
<?php endif; ?>

<b>Nota: No responda este correo, ya que este es generado automáticamente por el sistema.</b>

Cordialmente,

Hospital Universitario del Valle "Evaristo García" E.S.E. <br>
Servicios Ambulatorios - Unidad Básica de Atención
<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/emails/datoscita.blade.php ENDPATH**/ ?>