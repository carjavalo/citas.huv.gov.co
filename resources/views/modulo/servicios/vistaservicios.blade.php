@extends('layouts/main')


<div class="container mt-4">
    <h2>Mostrar informacion de {{ $item->servnomb }}</h2>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm text-center">
                        <thead>
                            <th>Id</th>
                            <th>Codigo Servicio</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Servicio</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->servcod }}</td>
                                <td>{{ $item->servnomb }}</td>
                                <td>{{ $item->estado }}</td>
                                <td>{{ $item->id_pservicios }}</td>
                            </tr>
                        </tbody>

                    </table>
                    <a href="{{ route('list_servicios') }}" class="btn btn-secondary mt-4"><i class="fa-solid fa-share-from-square"></i>
                        Cancelar
                    </a>

                  
                </div>
              </div>
        </div>
    </div>
</div>
