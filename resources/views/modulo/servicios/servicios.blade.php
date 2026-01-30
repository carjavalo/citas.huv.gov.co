@extends('layouts.menuagendacitas')
@section('content')
  <div class="container mt-4">
       <h2>Crud de Especialidades</h2>
       <div class="row">
           <div class="col">
            <div class="card">
              <div class="card-body">
                 <a href="{{route('create_servicios')}}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Agregar
                 </a>
                <!-- <a href="{{route('list_servicios')}}" class="btn btn-secondary">
                     <i class="fa-regular fa-share-from-square"></i> Volver
                 </a>-->
                 <hr>
                    <table class="table table-sm table-bordered text-center">
                         <thead>
                             <tr>
                                 <th>Id</th>
                                 <th>Cod Servicio</th>
                                 <th>Nombre</th>
                                 <th>Estado</th>
                                 <th>Id_Servicio</th>
                                 <th>Acciones</th>
 
                             </tr>
                         </thead>
                         <tbody>
                            @forelse ($items as $item)
                                
                                <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->servcod }}</td>
                                <td>{{ $item->servnomb }}</td>
                                <td>{{ $item->estado }}</td>
                                <td>{{ $item->id_pservicios }}</td>
                                <td>
                                    <form action="" method="post">
                                        <a href="{{ route('vista_servicios', $item->id) }}" class="btn btn-info"><i class="fa-solid fa-list"></i>
                                            Mostrar
                                        </a>
                                        <a href="{{ route('edit_servicios', $item->id) }}" class="btn btn-warning"><i class="fa-solid fa-pen-to-square"></i>
                                          Editar
                                        </a>
                                       <button class="btn btn-danger"><i class="fa-solid fa-trash"></i>
                                             Borrar
                                       </button>
                                    </form>

                                </td>

                              </tr>

                            @empty
                                <tr>
                                 <td>No hay datos en la Tabla</td>
                               </tr>
                              
                            @endforelse   
                         </tbody>

                  </table>
                  <div class="d-flex justify-content-end">
                    {{ $items->links() }}
                  </div>
              </div>
            </div>

           </div>
       </div> 
  </div>
    
@endsection