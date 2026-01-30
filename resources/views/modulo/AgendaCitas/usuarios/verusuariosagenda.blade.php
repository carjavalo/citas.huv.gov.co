@extends('layouts.menuagendacitas')
@section('content')
<div class="container mt-4">
    <h2>Modificar Datos de los Usuarios</h2>
    <div class="row">


        <div class="col-md-10">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title" style="font-weight: bold;">Datos Registrados</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
                <!-- /.card-tools -->
              </div>
              <!-- /.card-header -->
              <div class="card-body" style="display: block;">
                <div class="container">
                    <div class="card">
                        <div class="card-body">
                           
                                @csrf
                              <!--method('POST')-->
                                <div class="row">
                                    <!-- Primera columna -->
                                    <div class="col-md-6">
                                        <label for="name">Nombre</label><b>*</b>
                                        <input type="text" name="name" value="{{$usuagenda->name}}" class="form-control" required autofocus autocomplete="off" > 
                                        @error('name')     
                                         <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                                        <label for="apellido1">apellido</label><b>*</b>
                                        <input type="text" name="apellido1"  value="{{$usuagenda->apellido1}}" class="form-control" required autocomplete="off">
                                        @error('apellido1')     
                                         <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                                        <label for="apellido2" autocomplete="off">apellido</label>
                                        <input type="text" name="apellido2"  value="{{$usuagenda->apellido2}}" class="form-control" >
                                        <label for="tdocumento">Tipo Documento</label><b>*</b>                                       
                                          <select id="tdocumento"  class="form-control" name="tdocumento" autocomplete="off">
                                              <option>
                                                  Seleccione...
                                              </option>
                                              @foreach ($tipo_idenficacion as $tipo_id)
                                                  <option value="{{ $tipo_id->id }}" {{ old('tdocumento') == $tipo_id->id ? 'selected' : '' }}>{{ $tipo_id->nombre }}</option>
                                              @endforeach
                                          </select>
                                          @error('tdocumento')     
                                           <small style="color:red">{{$message}}</small>                                                                              
                                          @enderror
                                        <label for="ndocumento">Documento</label><b>*</b>
                                        <input type="text" name="ndocumento" value="{{$usuagenda->ndocumento}}" class="form-control" required autocomplete="off">
                                        @error('ndocumento')     
                                          <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                                  </div>
                
                                    <!-- Segunda columna -->
                                    <div class="col-md-6">
                                      <label for="eps">Eps</label><b>*</b>
                                        <select id="eps" class="form-control" name="eps" autocomplete="off">
                                            <option value="">Seleccione...</option>
                                            @foreach ($aseguradoras as $eps)
                                                <option value="{{ $eps->id }}" {{ old('eps') == $eps->id ? 'selected' : '' }}>{{ $eps->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('eps')     
                                        <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror                                                                            
                                        <label for="">Telefono</label><b>*</b>
                                        <input type="text" name="telefono1" value="{{$usuagenda->telefono1}}" class="form-control" required autofocus autocomplete="off">
                                        @error('telefono1')     
                                           <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                                        <label for="">Email</label><b>*</b>
                                        <input type="email" name="email"  value="{{$usuagenda->email}}" class="form-control" required autofocus autocomplete="off">
                                        @error('email')     
                                        <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror                                     
                                        <label for="password">Password</label><b>*</b>
                                        <input type="password" name="password" class="form-control" required autofocus autocomplete="off">
                                        @error('password')     
                                        <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                                        <label for="password">Confirmar Password</label>
                                        <input type="Password" name="password_confirmation"  class="form-control" required autofocus autocomplete="off">
                                        @error('password_confirmation')     
                                        <small style="color:red">{{$message}}</small>                                                                              
                                        @enderror
                
                                      <!--  <div class="form-check mt-4">
                                            <input type="checkbox" id="estado" name="estado" value="1" class="form-check-input">
                                            <label for="estado" class="form-check-label">Estado</label>
                                        </div>-->
                                    </div>
                                </div>
                
                                <!-- Botones debajo del formulario -->
                                <div class="mt-4">
                                    
                                    <a href="{{route('usuarios_agenda')}}" class="bi bi-x-circle btn btn-secondary">Cancelar</a>
                                </div>
                                                   </div>
                    </div>
                </div>
               
              </div>
             <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>        
       
    </div>
</div>

    <div class="row">
        <h1></h1>
    </div>    
@endsection