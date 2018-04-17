<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entidad;

class EntidadesController extends Controller
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
        
        return view('entidades.index')->with('params', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'Nombre' => 'required|max:255|unique:entidades,deleted_at,null'
        ]);
        
        $entity = new Entidad($request->all());
        $entity->save();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Entidad::orderBy('Nombre')->get();
        
        $xml=  "<?xml version='1.0' encoding='UTF-8'?>\n";
        $xml.=  "<rows pos='0'>";
        
        foreach($data as $i => $d){
            $xml.= "<row id = '$d->id'>";
            $xml.= "<cell>" . ($i+1) . "</cell>";
            $xml.= "<cell>" . htmlspecialchars("<i class='fa fa-2x fa-pencil' onclick='v.View(" . $d->id . ")'></i>"). "</cell>";
            $xml.= "<cell>" . htmlspecialchars("<i class='fa fa-2x fa-trash-o' onclick='v.Delete(" . $d->id . ")'></i>"). "</cell>";
            $xml.= "<cell>" .($d->Nombre)."</cell>";
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
        $entity = Entidad::find($id);
        
        return response()->json([
                    'entity' => $entity, 
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
        $entity = Entidad::find($id);
        $entity->fill($request->all());
        $entity->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $entity = Entidad::find($id);
        $entity->delete();
    }
}
