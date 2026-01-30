@extends('layouts/main')


<div class="container mt-4">
    <h2>Agregar Nueva Especialidad</h2>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                  <form action="{{ route('store_servicios') }}" method="post">
                     @csrf
                     @method('POST')
                     <label for="servcod">Codigo del Servicio</label>
                     <input type="text" name='servcod' id='servcod' class="form-control " required>
                     <label for="servcod">Nombre del Servicio</label>
                     <input type="text" name='servnomb' id='servnomb' class="form-control" required>
                     <label for="servcod">Codigo del Area</label>
                     <input type="text" name='id_pservicios' id='id_pservicios' class="form-control" required>
                     <label for="estado">Estado</label>
                     <input type="checkbox" id="estado" name="estado" value="1" class="form-check-input" required>
                     <!--<input type="text" name='estado' id='estado' class="form-control">-->
                    
                      <div class="mt-4">
                         <button class="btn btn-primary mt-3">Agregar</button>
                         <a href="{{route('list_servicios')}}" class="btn btn-secondary mt-3">Cancelar</a>
                      </div>  
                  </form>
                </div>
              </div>
        </div>
    </div>
</div>
