<?php

namespace App\Http\Controllers;

use App\Models\servicios;
use Illuminate\Http\Request;

class servicioscontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $items = servicios::paginate(12);
      return view('modulo/servicios/servicios', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('modulo/servicios/createservicios');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      /* $item = new servicios(); 
       $item->servcod =  $request->servcod;
       $item->servnomb = $request->servnomb;
       $item->estado = "false";
       $item->id_pservicios =$request->id_pservicios;
       $item->save();
       return to_route('servicios');*/

       try {
            $request->validate([
                'servcod' => 'required|string|max:255|unique:servicios',
                'servnomb' => 'required|string|max:255',
                'estado'  => 'required|boolean',
                'id_pservicios'=> 'required|integer|min:1|max:120',


            ]);
    
            User::create([
                'servcod' => $request->servcod,
                'servnomb' => $request->servnomb,
                'estado' => $request->estado,
                'id_pservicios' => $request->id_pservicios,
                //'email' => $request->email,
                //'password' => Hash::make($request->password),
            ]);
        } catch (\Throwable $th) {

            dd ($th);
        }
       






    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = servicios::find($id);
        return view('modulo/servicios/vistaservicios' , compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = servicios::find($id);
        return view('modulo/servicios/editservicios' , compact('item'));
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
        
        // $item = User::find($id);
        // Validación de los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);
         
        // Encontrar el usuario por su ID
        $user = User::findOrFail($id);
         // Actualizar los datos del usuario
         $user->name = $validatedData['name'];
         $user->email = $validatedData['email'];
    
           // Actualizar la contraseña solo si se proporciona
           if (!empty($validatedData['password'])) {
            $user->password = bcrypt($validatedData['password']);
        }
    
        // Guardar los cambios en la base de datos
        $user->save();
    
        // Redirigir o responder con un mensaje
       
        // return response()->json(['message' => 'Usuario actualizado correctamente'], 200);  
        
        
        
        
        
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
