@extends('layouts/main')


<div class="container mt-4">
    <h2>Actualizar Especialidad</h2>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                  <form action="{{ route('save_servicios', $item->id) }}" method="post">
                     @csrf
                     @method('PUT')
                     <label for="servcod">Codigo del Servicio</label>
                     <input type="text" name='servcod' id='servcod' class="form-control" required value="{{ $item->servcod}}">
                     <label for="servcod">Nombre del Servicio</label>
                     <input type="text" name='servnomb' id='servnomb' class="form-control" required value="{{ $item->servnomb}}">
                     <label for="servcod">Codigo del Area</label>
                     <input type="text" name='id_pservicios' id='id_pservicios' class="form-control" required value="{{ $item->id_pservicios}}">
                     <label for="estado">Estado</label>
                     <input type="checkbox" id="estado" name="estado" value="1" class="form-check-input" required value="{{ $item->estado}}">
                     <!--<input type="text" name='estado' id='estado' class="form-control">-->
                    
                      <div class="mt-4">
                         <button class="btn btn-warning mt-3">Actualizar</button>
                         <a href="{{route('list_servicios')}}" class="btn btn-secondary mt-3">Cancelar</a>
                      </div>  
                  </form>
                </div>
              </div>
        </div>
    </div>
</div>
