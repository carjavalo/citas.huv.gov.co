<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Models\User;
use App\Models\eps;
use App\Models\tipo_identificacion;
use Illuminate\Support\Facades\Hash;

class usuagendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     * 
     */
    public $usuagendas, $tipo_idenficacion, $aseguradoras; 


    public function index()
    {
                      
        //$usuagendas = User::orderBy('id','asc')->paginate(100000);
      //  $usuagendas = User::all();
        $usuagendas = User::paginate(10);
        return view('modulo.AgendaCitas.usuarios.usuagenda', compact('usuagendas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    /* public function render()
    {
        return view('modulo.AgendaCitas.usuarios.crearusuagenda', [
            'aseguradoras' => eps::where('estado','=',true)->get(),
            'tipo_idenficacion' => tipo_identificacion::all(),
        ])->layout('layouts.menuagendacitas');
    }*/
   
    public function create()
    {   
        $tipo_idenficacion = Tipo_identificacion::all();
        $aseguradoras = eps::all();
       // $usuarioscre = User::orderBy('created_at', 'desc')->get();
    


        return view('modulo.AgendaCitas.usuarios.crearusuagenda', compact('tipo_idenficacion','aseguradoras'/*,'usuarioscre'*/)); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        try {
            $request->validate([

                'name' => 'required|string|max:250',
                'apellido1' => 'required|string|max:250',
                'apellido2' => 'nullable|string|max:250', // No siempre es obligatorio
                'tdocumento' => 'required',
                'ndocumento' => 'required|numeric|unique:users,ndocumento',
                'eps' => 'required', // Si es opcional
                'telefono1' => 'required|numeric',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',            
            ]);
    
            User::create([
                
                'name' => $request->name,
                'apellido1' => $request->apellido1,
                'apellido2' => $request->apellido2,
                'tdocumento' => $request->tdocumento,
                'ndocumento' => $request->ndocumento,
                'eps' => $request->eps,
                'telefono1' => $request->telefono1,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (\Throwable $th) {
          //  dd ($th);
        }
       

        return redirect()->route('usuarios_agenda')
        ->with('success', 'Usuario creado correctamente.')
        ->with('icono','success');


       }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipo_idenficacion = Tipo_identificacion::all();
        $aseguradoras = eps::all();
       $usuagenda = User::find($id);
        return view('modulo.AgendaCitas.usuarios.verusuariosagenda', compact('usuagenda','tipo_idenficacion','aseguradoras'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
