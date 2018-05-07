<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Documento;
use App\Comentario;
use App\Version;
use App\Entidad;
use App\Usuario;

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
        
        if(auth()->user()->isPublisher()) 
            $entities = Entidad::orderBy('Nombre')->get();
        elseif(auth()->user()->isSuper())
            $entities = Usuario::find(auth()->user()->id)->entidades()->get();
        elseif(auth()->user()->isReviewer())
            $entities = Usuario::find(auth()->user()->padre_id)->entidades()->get();
        elseif(auth()->user()->isUser()) 
            $entities = Entidad::where('id', auth()->user()->entidad_id)->get();
        
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
        $comment->contenido = UpperCase($request->contenido);
        $comment->save();
        
        if(auth()->user()->isUser()){
            $super = Usuario::where('rol_id', 3)->whereHas('entidades', function($query){
                $query->where('entidad_id', auth()->user()->entidad_id);
            })->get();
            
            $rev = Usuario::where('rol_id', 4)->whereIn('padre_id', $super->pluck('id'))->get();
            
            $mails = $super->merge($rev)->pluck('Correo');
            
        }else{
            
            $mails[] = $comment->version->documento->usuario->Correo;
            
        }
        
        if($mails){
            $remit = "Sistema de Libros Blancos";
            $subject = "Documento actualizado";
            $content = "Por medio del presente se le comunica que el documento con nombre "
                    . "<b>" . $comment->version->documento->nombre . "</b>, "
                    . "de la entidad: <b>" . $comment->version->documento->entidad->Nombre . "</b> "
                    . "ha sido actualizado. "
                    . "<p><i>$comment->contenido</i></p>"
                    . "<p>Puede proceder a revisar sus detalles dentro del "
                    . "<a href = '" . $request->url() . "' target = '_blank'>Sistema de Libros Blancos</a>";

            foreach($mails as $m){
                insertMail($remit, $m, $subject, $content);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $type)
    {
        $documents = Version::where('documento_id', $id)->where('tipo', $type)->orderBy('id', 'desc')->with('autor')->get();

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
    public function update(Request $request, $id, $action)
    {
        $doc = Documento::findOrFail($id);
        
        if($action == "review"){
            $doc->estatus_id =  1;
            $doc->reviewed_at = now();
            $doc->reviewed_by = auth()->user()->id;
            
        }elseif($action == "validate"){
            $doc->estatus_id =  2;
            $doc->validated_at = now();
            $doc->validated_by = auth()->user()->id;
        }elseif($action == "publish"){
            $doc->estatus_id =  3;
            $doc->published_at = now();
            $doc->published_by = auth()->user()->id;
        }
        
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
