use App\Http\Controllers\ProfileController;
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');
});
<?php
use App\Models\Pservicio;
use App\Models\solicitudes;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Citas\Consulta as CitasUsuario;
use App\Http\Livewire\Citas\ConsultaGeneral as ConsultarCitas;
use App\Http\Livewire\Citas\Solicitar as SolicitarCita;
use App\Http\Livewire\Usuarios\Consulta as UsuariosConsulta;
use App\Http\Livewire\Usuarios\Registro as UsuariosRegistro;
use App\Http\Livewire\Eps\Consulta as ConsultarEps;
use App\Http\Livewire\Configuracion\Home as Configuracion;
use App\Http\Livewire\GodsonRequestComponent;
use App\Http\Livewire\Reporte\Agente\Procesado;
use App\Http\Controllers\reportecitas;
use App\Http\Controllers\servicioscontroller;
use App\Http\Controllers\DocumentoController;
use App\Http\Livewire\Servicios\Consulta as ConsultarServicios;
use App\Http\Livewire\Sedes\Consulta as ConsultarSedes;
use App\Http\Livewire\Pservicios\Consulta as ConsultarPservicios;
use App\Http\Livewire\Especialidades\Consulta as ConsultarEspecialidades;
use App\Http\Livewire\Campanas\Consulta as ConsultarCampanas;
use App\Http\Livewire\Roles\Consulta as ConsultarRoles;
use App\Http\Livewire\Operando\Consulta as OperandoConsulta;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/registro', UsuariosRegistro::class)->name('registro');

// Ruta para capturar accesos directos a /Documentos/* (URLs antiguas o directas)
Route::get('/Documentos/{path}', [DocumentoController::class, 'verDirecto'])->where('path', '.*')->middleware(['auth:sanctum', 'verified']);

Route::get('/prueba', function(){
    $solicitudes = solicitudes::all();
    return $solicitudes;
    
});


Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Ruta para visualizar documentos con espacios y caracteres especiales
    Route::get('/documento/ver/{path}', [DocumentoController::class, 'ver'])->name('documento.ver')->where('path', '.*');

    Route::get('/consulta/mis-solicitudes',     CitasUsuario::class)->name('cita.consulta');
    Route::get('/consulta/solicitudes',         ConsultarCitas::class)->name('cita.consultas');/* ******* */
    Route::get('/solicitar',                    SolicitarCita::class)->name('cita.solicitar');
    Route::get('/usuarios/consulta',            UsuariosConsulta::class)->name('usuarios.consulta');
    Route::get('/configuracion/eps/consulta',   ConsultarEps::class )->name('configuracion.eps.consulta');
    Route::get('/configuracion/servicios/consulta', ConsultarServicios::class)->name('configuracion.servicios');
    Route::get('/configuracion/sedes/consulta', ConsultarSedes::class)->name('configuracion.sedes');
    Route::get('/configuracion/pservicios/consulta', ConsultarPservicios::class)->name('configuracion.pservicios');
    Route::get('/configuracion/especialidades/consulta', ConsultarEspecialidades::class)->name('configuracion.especialidades');
    Route::get('/configuracion/campanas/consulta', ConsultarCampanas::class)->name('configuracion.campanas');
    Route::get('/configuracion/roles/consulta', ConsultarRoles::class)->name('configuracion.roles');
    Route::get('/operando/sql', OperandoConsulta::class)->name('operando.sql');
    Route::get('reporte/solicitudes/general', Procesado::class)->name('reporte.solicitudes.procesado');  
    Route::get('obstetricia', GodsonRequestComponent::class)->name('obstetricia');
    Route::get('/reporte/especialidades', function() { return view('reporte.especialidades'); })->name('reporte.especialidades');
    Route::get('/reporte/ingresos', \App\Http\Livewire\Reporte\Ingresos::class)->name('reporte.ingresos');

    //rutas para gestionar servicios   
    Route::get('/reportecitas/export', [reportecitas::class, 'reportecitasExport'])->name('reporte.solicitudes.reportecitas');
    Route::get('modulo/servicios/servicios', [servicioscontroller::class,'index'])->name('list_servicios');
    Route::get('modulo/servicios/create_servicios', [servicioscontroller::class,'create'])->name('create_servicios');
    Route::post('modulo/servicios/store_servicios', [servicioscontroller::class,'store'])->name('store_servicios');
    Route::get('modulo/servicios/vista_servicios/{id}', [servicioscontroller::class,'show'])->name('vista_servicios');
    Route::get('modulo/servicios/edit_servicios/{id}', [servicioscontroller::class,'edit'])->name('edit_servicios');
    Route::put('modulo/servicios/save_servicios/{id}', [servicioscontroller::class,'update'])->name('save_servicios');
    //rutas para agendascitas
    Route::get('modulo/AgendaCitas/menuagenda', 'App\Http\Controllers\agendarController@index')->name('menuagenda');
    Route::get('modulo/AgendaCitas/usuarios/usuagenda', 'App\Http\Controllers\usuagendaController@index')->name('usuagenda');
    Route::get('modulo/AgendaCitas/usuarios/usuagenda', [App\Http\Controllers\usuagendaController::class,'index'])->name('usuarios_agenda');
    Route::get('modulo/AgendaCitas/usuarios/crearusuagenda', [App\Http\Controllers\usuagendaController::class,'create'])->name('crea_usuarios_agenda');
    Route::post('modulo/AgendaCitas/usuarios/crearusuagenda', [App\Http\Controllers\usuagendaController::class,'store'])->name('store_usuarios_agenda');
    Route::get('modulo/AgendaCitas/usuarios/{id}', [App\Http\Controllers\usuagendaController::class,'show'])->name('show_usuarios_agenda');
   
   
   
});


/*Route::get('/roporte2', [reportecitas::class, 'metodo'] );

route::get('/reportecitas/export', [reportecitas::class, 'reportecitasExport'])->name('reporte.solicitudes.reportecitas');
Route::get('modulo/servicios/servicios', [servicioscontroller::class,'index'])->name('list_servicios');
Route::get('modulo/servicios/create_servicios', [servicioscontroller::class,'create'])->name('create_servicios');
Route::post('modulo/servicios/store_servicios', [servicioscontroller::class,'store'])->name('store_servicios');
Route::get('modulo/servicios/vista_servicios/{id}', [servicioscontroller::class,'show'])->name('vista_servicios');
Route::get('modulo/servicios/edit_servicios/{id}', [servicioscontroller::class,'edit'])->name('edit_servicios');
Route::put('modulo/servicios/save_servicios/{id}', [servicioscontroller::class,'update'])->name('save_servicios');*/

//rutas para citas
//Route::get('modulo/AgendaCitas/menuagenda', 'App\Http\Controllers\agendarController@index')->name('menuagenda');
//Route::get('modulo/AgendaCitas/menuagenda', [agendarController::class,'index'])->name('menuagenda');






Route::middleware(['auth:sanctum', 'verified'])->get('/', function () {
    if (Auth::user()->hasRole('Hospital'))
        return redirect()->to('obstetricia');
    return view('dashboard');
})->name('dashboard');

require_once __DIR__ . '/jetstream.php';