<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Gestion de Citas</title>
  <style> 
    .imagen{
        background-image: url("<?php echo e(asset('img/huv_fondo_2.jpg')); ?>"); 
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        min-height: calc(100vh - 120px);
    }
    .navbar-custom {
        background-color: #2c4370;
    }
    .dropdown-menu-custom {
        background-color: white;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .dropdown-menu-custom .dropdown-item {
        color: #2c4370;
        padding: 10px 20px;
    }
    .dropdown-menu-custom .dropdown-item:hover {
        background-color: #f0f4f8;
    }
    .dropdown-menu-custom .dropdown-item i {
        margin-right: 8px;
        color: #2c4370;
    }
    .nav-link-custom {
        color: white !important;
        padding: 0.5rem 1rem;
        transition: all 0.3s;
    }
    .nav-link-custom:hover {
        background-color: rgba(255,255,255,0.1);
        border-radius: 4px;
    }
    .btn-salir {
        background-color: rgb(153, 7, 7);
        color: white !important;
        border-radius: 4px;
    }
    .btn-salir:hover {
        background-color: rgb(120, 5, 5);
    }
  </style>
 
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo e(url('plugins/fontawesome-free/css/all.min.css')); ?>">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <!-- Iconos de Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- jQuery -->
  <script src="<?php echo e(url('plugins/jquery/jquery.min.js')); ?>"></script>
  <!-- Sweetalert2-->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo e(url('/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(url('/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(url('/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">
</head>
<body>
  
  <!-- Navbar Horizontal -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="<?php echo e(route('menuagenda')); ?>">
        <img src="<?php echo e(url('dist/img/logo.jpg')); ?>" alt="Logo" class="rounded-circle mr-2" style="width: 40px; height: 40px;">
        <span class="font-weight-bold">AGEND Medical</span>
      </a>
      
      <!-- Toggle button for mobile -->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu Items -->
      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav mr-auto">
          
          <!-- Usuarios Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link nav-link-custom dropdown-toggle" href="#" id="usuariosDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-people-fill mr-1"></i> Usuarios
            </a>
            <div class="dropdown-menu dropdown-menu-custom" aria-labelledby="usuariosDropdown">
              <a class="dropdown-item" href="<?php echo e(route('usuarios_agenda')); ?>">
                <i class="bi bi-person-lines-fill"></i> Listado de Pacientes
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-telephone"></i> Secretaria
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-mortarboard"></i> Profesionales
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-bar-chart-line-fill"></i> Reportes
              </a>
            </div>
          </li>

          <!-- Agenda Medica Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link nav-link-custom dropdown-toggle" href="#" id="agendaDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-terminal-plus mr-1"></i> Agenda Médica
            </a>
            <div class="dropdown-menu dropdown-menu-custom" aria-labelledby="agendaDropdown">
              <a class="dropdown-item" href="#">
                <i class="bi bi-hourglass-split"></i> Horarios
              </a>
              <a class="dropdown-item" href="<?php echo e(route('list_servicios')); ?>">
                <i class="bi bi-heart-pulse"></i> Especialidades
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-hospital"></i> Consultorios
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-clipboard-plus"></i> Asign. Consultorios
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-bar-chart-line-fill"></i> Reportes
              </a>
            </div>
          </li>

          <!-- Turnero Dropdown -->
          <li class="nav-item dropdown">
            <a class="nav-link nav-link-custom dropdown-toggle" href="#" id="turneroDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-shift-fill mr-1"></i> Turnero
            </a>
            <div class="dropdown-menu dropdown-menu-custom" aria-labelledby="turneroDropdown">
              <a class="dropdown-item" href="#">
                <i class="bi bi-briefcase"></i> Servicios
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-hand-index-fill"></i> Asignación Servicios
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-person-raised-hand"></i> Clientes
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-cast"></i> Configuración Pantallas
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-gear"></i> Configuración
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-list-check"></i> Calificación
              </a>
              <a class="dropdown-item" href="#">
                <i class="bi bi-bar-chart-line-fill"></i> Reportes
              </a>
            </div>
          </li>
        </ul>

        <!-- Right Side - User Info and Exit -->
        <ul class="navbar-nav ml-auto align-items-center">
          <li class="nav-item mr-3 d-flex align-items-center">
            <img src="<?php echo e(Auth::user()->profile_photo_url); ?>" alt="<?php echo e(Auth::user()->name); ?>" class="rounded-circle mr-2" style="width: 35px; height: 35px; object-fit: cover; border: 2px solid white;">
            <span class="text-white"><?php echo e(Auth::user()->name); ?> <?php echo e(Auth::user()->apellido1); ?></span>
          </li>
          <li class="nav-item">
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-link btn-salir px-3">
              <i class="bi bi-door-closed-fill mr-1"></i> Salir al Menú Principal
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <?php if( ($message= Session::get('success')) &&  ($icono = Session::get('icono')) ): ?>
   <script>
    Swal.fire({
                  position: "top-end",
                  icon: "<?php echo e($icono); ?>",
                  title: "<?php echo e($message); ?>",
                  showConfirmButton: false,
                 timer: 1500
              });
   </script>          
  <?php endif; ?> 

  <!-- Content Wrapper -->
  <div class="imagen">
    <div class="container-fluid py-4">
      <?php echo $__env->yieldContent('content'); ?>        
    </div>
  </div>

  <!-- Main Footer -->
  <footer class="text-white text-center py-3" style="background-color: #2c4370;">
    <strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io" class="text-white">Gestión de Servicios</a>.</strong> Todos los derechos reservados.
  </footer>

<!-- REQUIRED SCRIPTS -->


<!-- Bootstrap 4 -->
<script src="<?php echo e(url('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

<!-- DataTables  & Plugins -->

<script src="<?php echo e(url('plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-buttons/js/dataTables.buttons.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/jszip/jszip.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/pdfmake/pdfmake.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/pdfmake/vfs_fonts.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.html5.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.print.min.js')); ?>"></script>
<script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.colVis.min.js')); ?>"></script>

<!-- AdminLTE App -->
<script src="<?php echo e(url('dist/js/adminlte.min.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\citas\resources\views/layouts/menuagendacitas.blade.php ENDPATH**/ ?>