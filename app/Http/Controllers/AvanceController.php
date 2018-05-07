<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Entidad;

class AvanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params[] = array("Header" => "#", "Width" => "40", "Attach" => "", "Align" => "center", "Sort" => "str", "Type" => "ro");
        $params[] = array("Header" => "Entidad", "Width" => "*", "Attach" => "txt", "Align" => "left", "Sort" => "str", "Type" => "ed");
        $params[] = array("Header" => "Total docs.", "Width" => "120", "Attach" => "txt", "Align" => "center", "Sort" => "str", "Type" => "ed", "Sum" => true);
        $params[] = array("Header" => "Docs. en revisión", "Width" => "120", "Attach" => "txt", "Align" => "center", "Sort" => "str", "Type" => "ed", "Sum" => true);
        $params[] = array("Header" => "Docs. en validación", "Width" => "120", "Attach" => "txt", "Align" => "center", "Sort" => "str", "Type" => "ed", "Sum" => true);
        $params[] = array("Header" => "Docs. en formato", "Width" => "120", "Attach" => "txt", "Align" => "center", "Sort" => "str", "Type" => "ed", "Sum" => true);
        $params[] = array("Header" => "Docs. concluidos", "Width" => "120", "Attach" => "txt", "Align" => "center", "Sort" => "str", "Type" => "ed", "Sum" => true);
        
        return view('reportes.avance')->with('params', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
            $xml.= "<cell>" .($d->Nombre)."</cell>";
            $xml.= "<cell>" .($d->documentos()->count())."</cell>";
            $xml.= "<cell>" .($d->documentos()->where('estatus_id', 0)->count())."</cell>";
            $xml.= "<cell>" .($d->documentos()->where('estatus_id', 1)->count())."</cell>";
            $xml.= "<cell>" .($d->documentos()->where('estatus_id', 2)->count())."</cell>";
            $xml.= "<cell>" .($d->documentos()->where('estatus_id', 3)->count())."</cell>";
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
