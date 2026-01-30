@extends('layouts.menuagendacitas')
@section('content')
    <div class="row">
      <h1>Crud de Usuarios </h1>  
    </div> 
    <br/>   
    <div class="row">
      <div class="col-md-10">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <a href="{{ route('crea_usuarios_agenda') }}" class="btn btn-primary"> 
              <i class="nav-icon fas bi bi-person-add"></i> <b>Agregar Usuarios</b>
            </a>
           <!--<h3 class="card-title">Listad de Usuarios Registrados</h3>-->
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
            <!-- /.card-tools -->
          </div>
          
          <!-- /.card-header 
          <div class="card-body">
            The body of the card
          </div>-->
          <table id="#example1"  class="table table-striped table-bordered table-hover table-sm " >
            <thead class="thead-dark">
           <tr>
             <th scope="col" style="text-align: center"><b>Nombre</b></th>
             <th scope="col"  style="text-align: center"><b>Apellido</b></th>
             <th scope="col"  style="text-align: center"><b>Apellido</b></th>
             <th scope="col"  style="text-align: center"><b>Documento</b></th>
             <th scope="col"  style="text-align: center"><b>telefono</b></th>
             <th scope="col"  style="text-align: center"><b>Eps</b></th>
             <th scope="col"  style="text-align: center"><b>Acciones</b></th>       
           </tr>
         </thead>
         <tbody>
          @foreach($usuagendas as $usuagenda) 
           <tr>
             <th>{{$usuagenda->name}}</th>
             <th>{{$usuagenda->apellido1}}</th>
             <th>{{$usuagenda->apellido2}}</th>
             <th>{{$usuagenda->ndocumento}}</th>
             <th>{{$usuagenda->telefono1}}</th>
             <th>{{$usuagenda->eps}}</th>
             <td>
                <form action="" >
                     <a href="{{ url('modulo/AgendaCitas/usuarios/'.$usuagenda->id) }}" class="btn btn-info btn-sm"><i class="bi bi-eye-fill"></i></a>
                     <a href="#"    class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i></a>
                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                 </form>
               </td>
            </tr>
            @endforeach 
          </tbody>
          </table>
         <script>
           $(function () {
                           $("#example1").DataTable({
                                                     "responsive": true, "lengthChange": false, "autoWidth": false,
                                                     "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                                                    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                           $('#example2').DataTable({
                                                     "paging": true,
                                                     "lengthChange": false,
                                                     "searching": false,
                                                     "ordering": true,
                                                     "info": true,
                                                     "autoWidth": false,
                                                     "responsive": true,
                                                     });
                                                        });  
          </script>          
         <div class="d-flex justify-content-end">
           {{$usuagendas->links()}}
          </div>
       </div>
       <!--  /.card--> 
      </div>
     
 </div>
@endsection