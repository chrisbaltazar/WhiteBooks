<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Comentario;
use App\Version;
use App\Entidad;


class ComentariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('comentarios.index');
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(auth()->user()->rol_id == 5) 
            $entities = Entidad::where('id', auth()->user()->entidad_id)->get();
        else
            $entities = Entidad::orderBy('Nombre')->get();
        
        return response()->json([
            'entities'  => $entities
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
            'version_id'    => 'required|numeric',
            'contenido' => 'required'
        ]);
        
        $comment = new Comentario($request->all());
        $comment->save();
        
        $doc = Documento::find($comment->version->documento_id);
        $doc->estatus_id = 0;
        $doc->save();
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $documents = Version::where('documento_id', $id)->orderBy('id', 'desc')->with('autor')->get();
        
        return response()->json([
            'documents' => $documents, 
            'path'      => asset('/')
        ]);
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
        $doc = Documento::findOrFail($id);
        
        $doc->estatus_id = 1;
        
        $doc->save();
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
