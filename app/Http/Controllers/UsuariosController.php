<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Usuario;
use App\Rol;
use App\Entidad;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params[] = array("Header" => "#", "Width" => "40", "Attach" => "", "Align" => "center", "Sort" => "str", "Type" => "ro");
        $params[] = array("Header" => "Ver", "Width" => "50", "Attach" => "", "Align" => "center", "Sort" => "str", "Type" => "ro");
        $params[] = array("Header" => "Borrar", "Width" => "50", "Attach" => "", "Align" => "center", "Sort" => "str", "Type" => "ro");
        $params[] = array("Header" => "Nombre", "Width" => "*", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        $params[] = array("Header" => "Correo", "Width" => "150", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        $params[] = array("Header" => "Entidad", "Width" => "*", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        $params[] = array("Header" => "Rol", "Width" => "100", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        $params[] = array("Header" => "Atiende", "Width" => "*", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        
        
        return view('usuarios.usu_index')->with('params', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles  = Rol::orderBy('id')->get();
        
        $all = Usuario::where('rol_id', '3')->orderBy('Nombre')->get();
        
        $entities = Entidad::orderBy('Nombre')->get();
       
        return response()->json([
                    'roles' => $roles, 
                    'all' => $all, 
                    'entidades' => $entities
                ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'Correo' => 'required|email|max:255', 
            'Password' => 'required|max:255|confirmed'
        ]);
       
        if(!Usuario::where('Correo', $request->Correo)->first()){
        
            $user = new Usuario($request->all());
            $user->Nombre = UpperCase($request->Nombre);
            $user->Password = md5($request->Password);
            $user->save();
            
        }else{
            abort(403, "Este correo ya se encuentra en uso");
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
        $data = Usuario::orderBy('Nombre')->get();
        
        $xml=  "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml.=  "<rows pos='0'>";
        
        foreach($data as $i => $d){
            $xml.= "<row id = '$d->id'>";
            $xml.= "<cell>" . ($i+1) . "</cell>";
            $xml.= "<cell>" . htmlspecialchars("<i class='fa fa-2x fa-pencil' onclick='v.View(" . $d->id . ")'></i>"). "</cell>";
            $xml.= "<cell>" . htmlspecialchars("<i class='fa fa-2x fa-trash-o' onclick='v.Delete(" . $d->id . ")'></i>"). "</cell>";
            $xml.= "<cell>" .($d->Nombre)."</cell>";
            $xml.= "<cell>" .($d->Correo)."</cell>";
            $xml.= "<cell>" .($d->Entidad->Nombre)."</cell>";
            $xml.= "<cell>" .($d->Rol->NombreRol)."</cell>";
            if($d->isSuper())
                $xml.= "<cell>" .($d->entidades->pluck('Nombre')->implode(','))."</cell>";
            elseif($d->isReviewer())
                $xml.= "<cell>" .(Usuario::find($d->padre_id)->entidades->pluck('Nombre')->implode(','))."</cell>";
            else
                $xml.= "<cell></cell>";
            $xml.= "</row>";
        }
            
        $xml.=  "</rows>";
        
        return response($xml)->header('Content-Type', 'text/xml');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Usuario::find($id);
        
        $roles  = Rol::orderBy('id')->get();
        
        $all = Usuario::where('rol_id', '3')->orderBy('Nombre')->get();
        
        $entities = Entidad::orderBy('Nombre')->get();
        
        $relations = $user->entidades()->pluck('entidad_id')->toArray();
        
//        dd($relations);
        
        return response()->json([
                    'user' => $user, 
                    'roles' => $roles, 
                    'all' => $all, 
                    'entidades' => $entities, 
                    'relations' => $relations
                ]);
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
        $request->validate([
            'Correo' => 'required|email|max:255', 
            'entidad_id' => 'required', 
            'Nombre' => 'required|max:255'
        ]);
        
        if(!Usuario::where('Correo', $request->Correo)->where('id', '<>', $id)->first()){
        
            $user = Usuario::find($id);
            $user->fill($request->all());
            $user->Nombre = UpperCase($request->Nombre);
            if($request->Password)
                $user->Password = md5($request->Password);
            $user->save();
            $user->entidades()->sync($request->entidades);
            
        }else{
            abort(403, "Este correo ya se encuentra en uso");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Usuario::find($id);
        $user->delete();
    }
}
